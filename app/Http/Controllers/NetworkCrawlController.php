<?php

namespace App\Http\Controllers;

use App\Models\MySql\NetworkCrawl;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\NetworkFirmMediaBuyer;
use App\Models\MySql\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NetworkCrawlController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $nwFirmType = $request->query('nw_firm_type', NetworkCrawl::NW_FIRM_TYPE_MONETIZATION);
        $nwFirmTable = $nwFirmType == NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY ? NetworkFirmMediaBuyer::TABLE : NetworkFirm::TABLE;

        $query = NetworkCrawl::query()
            ->from(NetworkCrawl::TABLE . ' as t1')
            ->leftJoin(Users::TABLE . ' as t2', 't2.id', '=', 't1.admin_id')
            ->leftJoin($nwFirmTable . ' as t3', 't3.id', '=', 't1.nw_firm_id')
            ->select([
                't1.id', 't1.network_firm_type as nw_firm_type', 't1.type', 't1.schedule_time', 't1.pull_type',
                't1.nw_firm_id', 't3.name as nw_firm_name', 't1.admin_id', 't2.name as admin_name', 't1.update_time'
            ])
            ->orderBy('t1.type')
            ->orderByDesc('t1.schedule_time')
            ->orderBy('t1.nw_firm_id')
            ->where('t1.network_firm_type', $nwFirmType);

        if (array_key_exists($request->query('type', -1), NetworkCrawl::getTypeMap())) {
            $query->where('t1.type', $request->query('type'));
        }
        if ($request->has('schedule_time')) {
            $query->where('t1.schedule_time', $request->query('schedule_time'));
        }
        if (array_key_exists($request->query('nw_firm_id', -1), NetworkFirm::getAllIntegratedNwFirmMap())) {
            $query->where('t1.nw_firm_id', $request->query('nw_firm_id'));
        }

        $paginator = $query->paginate($request->query('page_size', 25), ['*'], 'page_no', $request->query('page_no', 1));

        $res = $this->parseResByPaginator($paginator);

        foreach ($res['list'] as $idx => $datum) {
            $res['list'][$idx]['admin_name'] = $datum['admin_name'] ?? '-';
            $res['list'][$idx]['nw_firm_name'] = $datum['nw_firm_name'] ?? '-';
        }

        return $this->jsonResponse($res);
    }

    /**
     * 获取导入时间的元数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function metaPullTime(Request $request): JsonResponse
    {
        $nwFirmType = $request->query('nw_firm_type', NetworkCrawl::NW_FIRM_TYPE_MONETIZATION);
        $data = NetworkCrawl::query()->select(['schedule_time as label', 'schedule_time as value'])
            ->where('network_firm_type', $nwFirmType)
            ->groupBy(['schedule_time'])
            ->get()
            ->toArray();
        return $this->jsonResponse($data);
    }

    /**
     * 可选的广告平台元数据
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function metaNwFirm(Request $request): JsonResponse
    {
        /* 默认搜变现平台 */
        $nwFirmType = $request->query('nw_firm_type', NetworkCrawl::NW_FIRM_TYPE_MONETIZATION);
        /* 0 代表搜全部，默认搜全部 */
        $type = $request->query('type', 0);

        /* 千万不要优化合并这份代码，因为 NetworkFirm 和 NetworkFirmMediaBuyer 的枚举值不同，合并不了 */
        if ($nwFirmType == NetworkCrawl::NW_FIRM_TYPE_MONETIZATION) {
            $query = NetworkFirm::query()->select(['id as value', 'name as label']);
            if ($type == 0) {
                $query->where('crawl_support', NetworkFirm::CRAWL_SUPPORT_DAY_YES)
                    ->orWhere('crawl_support_hour', NetworkFirm::CRAWL_SUPPORT_HOUR_YES);
            } else {
                $query = $type == NetworkCrawl::TYPE_DAY
                    ? $query->where('crawl_support', NetworkFirm::CRAWL_SUPPORT_DAY_YES)
                    : $query->where('crawl_support_hour', NetworkFirm::CRAWL_SUPPORT_HOUR_YES);
            }
        } else {
            $query = NetworkFirmMediaBuyer::query()->select(['id as value', 'name as label']);
            if ($type == 0) {
                $query->where('crawl_support', NetworkFirmMediaBuyer::CRAWL_SUPPORT_DAY_YES)
                    ->orWhere('crawl_support_hour', NetworkFirmMediaBuyer::CRAWL_SUPPORT_HOUR_YES);
            } else {
                $query = $type == NetworkCrawl::TYPE_DAY
                    ? $query->where('crawl_support', NetworkFirmMediaBuyer::CRAWL_SUPPORT_DAY_YES)
                    : $query->where('crawl_support_hour', NetworkFirmMediaBuyer::CRAWL_SUPPORT_HOUR_YES);
            }
        }
        return $this->jsonResponse($query->get()->toArray());
    }

    public function show($id): JsonResponse
    {
        $record = NetworkCrawl::query()
            ->from(NetworkCrawl::TABLE . ' as t1')
            ->leftJoin(NetworkFirm::TABLE . ' as t2', 't2.id', '=', 't1.nw_firm_id')
            ->leftJoin(NetworkFirmMediaBuyer::TABLE . ' as t3', 't3.id', '=', 't1.nw_firm_id')
            ->select(['t1.type', 't1.schedule_time', 't1.pull_type', 't1.nw_firm_id'])
            ->selectRaw("IFNULL(t2.name, t3.name) as nw_firm_name")
            ->where('t1.id', $id)
            ->firstOrFail();
        return $this->jsonResponse($record);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'schedule_time'     => ['required', 'regex:/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/'],
            'nw_firm_id_list'   => ['required', 'array'],
            'nw_firm_type'      => ['required', Rule::in(array_keys(NetworkCrawl::getNwFirmTypeMap()))],
            'type'              => ['required', Rule::in(array_keys(NetworkCrawl::getTypeMap()))],
            'pull_type'         => ['required', Rule::in(array_keys(NetworkCrawl::getPullTypeMap()))],
        ];

        $this->validate($request, $rules);

        $data = [
            'network_firm_type' => $request->input('nw_firm_type'),
            'type'              => $request->input('type'),
            'schedule_time'     => $request->input('schedule_time'),
            'pull_type'         => $request->input('pull_type'),
            'admin_id'          => auth()->id(),
            'status'            => NetworkCrawl::STATUS_UP,
        ];

        $nwFirmIdList = $request->input('nw_firm_id_list');
        $addition = [];
        foreach ($nwFirmIdList as $nwFirmId) {
            if (($data['network_firm_type'] === NetworkCrawl::NW_FIRM_TYPE_MEDIA_BUY && $nwFirmId < 100000) ||
                ($data['network_firm_type'] === NetworkCrawl::NW_FIRM_TYPE_MONETIZATION && $nwFirmId >= 100000)) {
                continue;
            }
            $data['nw_firm_id'] = $nwFirmId;
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');
            $addition[] = $data;
        }

        NetworkCrawl::query()->insert($addition);

        return $this->jsonResponse([], 1);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $networkCrawl = NetworkCrawl::query()->where('id', $id)->firstOrFail();

        $rule = [
            'schedule_time' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/'],
            'pull_type' => ['required', 'in:' . NetworkCrawl::PULL_YESTERDAY . ',' . NetworkCrawl::PULL_BEFORE_YESTERDAY],
        ];

        $this->validate($request, $rule);

        $networkCrawl->update([
            'schedule_time' => $request->get('schedule_time'),
            'update_time' => date('Y-m-d H:i:s'),
            'pull_type' => $request->get('pull_type'),
            'admin_id' => auth('api')->id(),
        ]);

        return $this->jsonResponse();
    }

    /**
     * 删除记录，支持多重删除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->validate($request, ['id_list' => 'required', 'array']);

        $idList = $request->get('id_list', []);
        NetworkCrawl::query()->whereIn('id', $idList)->delete();
        return $this->jsonResponse();
    }
}
