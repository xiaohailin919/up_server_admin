<?php

namespace App\Http\Controllers;

use App\Helpers\Export;
use App\Models\MySql\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\MySql\Placement as PlacementModel;
use Illuminate\Validation\Rule;
use Proto\RpcWaterFallServiceClient;
use Grpc;
use Proto\SqsQueueParam;
use Illuminate\Support\Facades\Log;

class PlacementController extends ApiController
{
    /**
     * 列表数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkAccessPermission('placement_list');

        $query = PlacementModel::query()
            ->from('placement as t1')
            ->leftJoin('publisher as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin('app as t3', 't3.id', '=', 't1.app_id')
            ->select([
                't1.id', 't1.uuid', 't1.name', 't3.uuid as app_uuid', 't3.name as app_name', 't2.id as publisher_id',
                't2.name as publisher_name', 't1.format', 't1.status', 't1.create_time',
                't1.update_time'
            ])->orderByDesc('id');

        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if ($request->has('publisher_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('publisher_name') . '%');
        }
        if ($request->has('app_id')) {
            $appId = $request->query('app_id');
            $query->where(static function ($subQuery) use ($appId) {
                $subQuery->where('t1.app_id', $appId)->orWhere('t3.uuid', $appId);
            });
        }
        if ($request->has('app_name')) {
            $query->where('t3.name', 'like', '%' . $request->query('app_name') . '%');
        }
        if ($request->has('id')) {
            $placementId = $request->query('id');
            $query->where(static function ($subQuery) use ($placementId) {
                $subQuery->where('t1.id', $placementId)->orWhere('t1.uuid', $placementId);
            });
        }
        if ($request->has('name')) {
            $query->where('t1.name', 'like', '%' . $request->query('name') . '%');
        }
        if (array_key_exists($request->query('format', -1), PlacementModel::getFormatMap())) {
            $query->where('t1.format', $request->query('format'));
        }
        if (array_key_exists($request->query('status', -1), PlacementModel::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }
        if (array_key_exists($request->query('system', -1), PlacementModel::getSystemMap())) {
            $query->where('t3.system', $request->query('system'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no'));
        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $key => $datum) {
            /* 兼容 app 数据丢失的情况 */
            if ($datum['app_uuid'] == null) {
                $data['list'][$key]['app_uuid'] = '';
                $data['list'][$key]['app_name'] = '';
            }
            /* 兼容 publisher 数据丢失的情况 */
            if ($datum['publisher_name'] == null) {
                $data['list'][$key]['publisher_id'] = 0;
                $data['list'][$key]['publisher_name'] = '';
            }
            /* Placement 使用 int 存储当前秒数，转为毫秒数 */
            $data['list'][$key]['create_time'] = $datum['create_time'] * 1000;
            $data['list'][$key]['update_time'] = $datum['update_time'] * 1000;
        }

        return $this->jsonResponse($data);
    }

    /**
     * 导出数据
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $request->query->set('page_no', 1);
        $request->query->set('page_size', 5000);
        $response = json_decode($this->index($request)->content(), true);
        $data = $response['data']['list'];

        $headerMap = [
            'id'             => __('common.id'),
            'uuid'           => __('common.placement.uuid'),
            'name'           => __('common.placement.name'),
            'app_uuid'       => __('common.app.uuid'),
            'app_name'       => __('common.app.name'),
            'publisher_id'   => __('common.publisher.id'),
            'publisher_name' => __('common.publisher.name'),
            'format'         => __('common.placement.format'),
            'status'         => __('common.placement.status'),
            'create_time'    => __('common.create_time'),
            'update_time'    => __('common.update_time'),
        ];

        foreach ($data as $key => $datum) {
            $data[$key]['format'] = PlacementModel::getFormatName($datum['format']);
            $data[$key]['status'] = PlacementModel::getStatusName($datum['status']);
            $data[$key]['create_time'] = date('Y-m-d H:i:s', $datum['create_time'] / 1000);
            $data[$key]['update_time'] = date('Y-m-d H:i:s', $datum['update_time'] / 1000);
        }

        Export::exportAsCsv($data, $headerMap, 'placement_' . date('Y_m_d_H_i_s') . '.csv');
    }

    /**
     * 单个数据
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $this->checkAccessPermission('placement_list');

        $placement = PlacementModel::query()->where('id', $id)->firstOrFail();
        $res = [
            'uuid' => $placement['uuid'],
            'name' => $placement['name'],
            'status' => $placement['status'],
        ];
        return $this->jsonResponse($res);
    }

    /**
     * 更新
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkAccessPermission('placement_edit');

        $this->validate($request, ['status' => ['required', Rule::in(array_keys(PlacementModel::getStatusMap(true)))]]);

        $placement = PlacementModel::query()->where('id', $id)->firstOrFail();

        $placement->update([
            'status' => $request->input('status'),
            'update_time' => time(),
        ]);

        //处理添加队列
        $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
            'credentials' => Grpc\ChannelCredentials::createInsecure()
        ]);
        $args = new SqsQueueParam();
        $args->setType(1);
        $args->setId(intval($id));
        $res = $client->PlacementSqsQueue($args)->wait();
        if ($res[1]->code !== 0) {
            Log::info("update placement quque failed:" . $res[1]->details);
        } else {
            Log::info("update placement quque succ");
        }

        return $this->jsonResponse();
    }
}
