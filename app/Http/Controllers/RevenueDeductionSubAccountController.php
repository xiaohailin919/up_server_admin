<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2019/1/22
 * Time: 19:27
 */

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\ReportBlackAssignment;
use App\Models\MySql\Users;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client;

use App\Models\MySql\Placement as PlacementModel;

class RevenueDeductionSubAccountController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->query('dimension', -1) === 'publisher') {
            return $this->indexForPublisher($request);
        }

        $query = ReportBlackAssignment::query()
            ->from(ReportBlackAssignment::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(App::TABLE . ' as t3', 't3.id', '=', 't1.app_id')
            ->leftJoin(PlacementModel::TABLE . ' as t4', 't4.id', '=', 't1.placement_id')
            ->leftJoin(Users::TABLE . ' as t5', 't5.id', '=', 't1.admin_user')
            ->select([
                't1.id', 't1.publisher_id', 't2.name as publisher_name',
                't1.app_id', 't3.name as app_name', 't3.uuid as app_uuid',
                't1.placement_id', 't4.name as placement_name', 't4.uuid as placement_uuid',
                't1.expected_value', 't1.status', 't1.utime as update_time',
                't1.admin_user as admin_id', 't5.name as admin_name'
            ])->where('t1.type', ReportBlackAssignment::TYPE_SUB)
            ->orderByDesc('utime');

        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if ($request->has('app_id')) {
            $tmp = $request->query('app_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            });
        }
        if ($request->has('placement_id')) {
            $tmp = $request->query('placement_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t4.id', $tmp)->orWhere('t4.uuid', $tmp);
            });
        }
        if (array_key_exists($request->query('status', -1), ReportBlackAssignment::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            $data['list'][$idx]['placement_name'] = $datum['placement_name'] ?? '-';
            $data['list'][$idx]['placement_uuid'] = $datum['placement_uuid'] ?? '-';
            $data['list'][$idx]['admin_name']     = $datum['admin_name']     ?? '-';
        }

        return $this->jsonResponse($data);
    }

    public function indexForPublisher(Request $request): JsonResponse
    {

        $query = Publisher::query()
            ->from(Publisher::TABLE . ' as t1')
            ->leftJoin(Users::TABLE . ' as t2', 't2.id', '=', 't1.admin_id')
            ->select([
                't1.id', 't1.id as publisher_id', 't1.name as publisher_name',
                't1.sub_account_distribution as expected_value',
                't1.admin_id', 't2.name as admin_name', 't1.update_time',
            ])
            ->where('t1.status', '!=', Publisher::STATUS_DELETE)
            ->where('t1.sub_account_parent', '!=', 0)
            ->where('t1.sub_account_distribution_switch', Publisher::SUB_ACCOUNT_SWITCH_ON);

        if ($request->has('publisher_id')) {
            $query->where('t1.id', $request->query('publisher_id'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            $data['list'][$idx]['app_id']         = 0;
            $data['list'][$idx]['app_name']       = '-';
            $data['list'][$idx]['app_uuid']       = '-';
            $data['list'][$idx]['placement_id']   = 0;
            $data['list'][$idx]['placement_name'] = '-';
            $data['list'][$idx]['placement_uuid'] = '-';
            $data['list'][$idx]['status']         = ReportBlackAssignment::STATUS_RUNNING;
        }

        return $this->jsonResponse($data);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'dimension'      => ['required', 'in:app,placement'],
            'publisher_id'   => ['required', 'exists:publisher,id'],
            'app_uuid'       => ['required_if:dimension,app', 'exists:app,uuid'],
            'placement_uuid' => ['required_if:dimension,placement', 'exists:placement,uuid'],
            'expected_value' => ['required', 'integer', 'min:0', 'max:1000'],
            'status'         => ['required', Rule::in(array_keys(ReportBlackAssignment::getStatusMap(true)))],
        ];

        $this->validate($request, $rules);

        $placementId = 0;
        if ($request->get('dimension') == 'placement') {
            $placement   = Placement::query()->where('uuid', $request->get('placement_uuid'))->firstOrFail(['id', 'uuid', 'publisher_id', 'app_id']);
            $appId       = $placement['app_id'];
            $placementId = $placement['id'];
            $parentPub   = $placement['publisher_id'];
        } else {
            $app       = App::query()->where('uuid', $request->get('app_uuid'))->firstOrFail(['id', 'publisher_id']);
            $appId     = $app['id'];
            $parentPub = $app['publisher_id'];
        }

        /* 校验开发者 */
        $publisherId = $request->get('publisher_id');
        if (!Publisher::query()->where('id', $publisherId)->where('mode', Publisher::MODE_BLACK)->where('sub_account_parent', $parentPub)->exists()) {
            return $this->jsonResponse(['publisher_id' => 'Publisher id error!'], 10000, '该开发者并非该应用所属开发者的黑盒子账号!');
        }

        /* 校验唯一 */
        if (ReportBlackAssignment::query()->where('publisher_id', $publisherId)->where('app_id', $appId)->where('placement_id', $placementId)->exists()) {
            return $this->jsonResponse([], 10000, '该记录已存在');
        }

        ReportBlackAssignment::query()->insert([
            'type'                 => ReportBlackAssignment::TYPE_SUB,
            'publisher_id'         => $publisherId,
            'app_id'               => $appId,
            'placement_id'         => $placementId,
            'country'              => '00',
            'assignment_type'      => ReportBlackAssignment::TYPE_ASSIGNMENT_DISCOUNT,
            'expected_value'       => $request->get('expected_value'),
            'random_range'         => 0,
            'maximum_compensation' => 1000,
            'admin_user'           => auth('api')->id(),
            'utime'                => date('Y-m-d H:i:s'),
            'ctime'                => date('Y-m-d H:i:s'),
            'status'               => $request->get('status'),
        ]);

        return $this->jsonResponse([], 1);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $dimension = $request->query('dimension', 'all');

        if ($dimension == 'publisher') {
            $data = Publisher::query()->where('id', $id)
                ->where('mode', Publisher::MODE_BLACK)
                ->where('sub_account_parent', '!=', 0)
                ->firstOrFail(['id', 'sub_account_distribution as expected_value'])->toArray();

            $data['publisher_id']    = $data['id'];
            $data['app_id']          = 0;
            $data['app_uuid']        = '-';
            $data['app_name']        = '-';
            $data['placement_id']    = 0;
            $data['placement_uuid']  = '-';
            $data['placement_name']  = '-';
            $data['assignment_type'] = ReportBlackAssignment::TYPE_ASSIGNMENT_DISCOUNT;
            $data['status']          = ReportBlackAssignment::STATUS_RUNNING;
        } else {
            $data = ReportBlackAssignment::query()
                ->from(ReportBlackAssignment::TABLE . ' as t1')
                ->leftJoin(App::TABLE . ' as t2', 't2.id', '=', 't1.app_id')
                ->leftJoin(Placement::TABLE . ' as t3', 't3.id', '=', 't1.placement_id')
                ->select([
                    't1.id', 't1.publisher_id', 't1.app_id', 't2.uuid as app_uuid', 't2.name as app_name',
                    't1.placement_id', 't3.uuid as placement_uuid', 't3.name as placement_name',
                    't1.assignment_type', 't1.expected_value', 't1.status'
                ])
                ->where('t1.id', $id)
                ->where('t1.type', ReportBlackAssignment::TYPE_SUB)
                ->firstOrFail()->toArray();

            $data['placement_uuid'] = $data['placement_uuid'] ?? '-';
            $data['placement_name'] = $data['placement_name'] ?? '-';
        }

        return $this->jsonResponse($data);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'expected_value' => ['required', 'integer', 'min:0', 'max:1000'],
            'status'         => ['required', Rule::in(array_keys(ReportBlackAssignment::getStatusMap(true)))]
        ];
        $this->validate($request, $rules);

        $dimension = $request->input('dimension', 'all');

        if($dimension == 'publisher'){
            $publisher = Publisher::query()
                ->where('id', $id)
                ->where('mode', Publisher::MODE_BLACK)
                ->where('sub_account_parent', '!=', 0)
                ->firstOrFail();
            $publisher->update([
                'sub_account_distribution' => $request->get('expected_value'),
                'update_time'              => time(),
                'admin_id'                 => auth('api')->id(),

            ]);
        } else {
            $record = ReportBlackAssignment::query()->where('id', $id)->where('type', ReportBlackAssignment::TYPE_SUB)->firstOrFail();
            $record->update([
                'expected_value' => $request->get('expected_value'),
                'status'         => $request->get('status'),
                'utime'          => date('Y-m-d H:i:s'),
                'admin_user'     => auth('api')->id()
            ]);
        }
        return $this->jsonResponse();
    }

    public function rerun(Request $request): JsonResponse
    {
        $appIdList = App::query()->get(['id'])->getQueueableIds();
        $placementIdList = Placement::query()->get(['id'])->getQueueableIds();
        $appIdList[] = 0;
        $placementIdList[] = 0;
        $lastDay = date('Ymd', time() - 24 * 60 * 60 * 2);

        $rules = [
            'publisher_id' => ['required', 'exists:publisher,id'],
            'app_id'       => ['required', Rule::in($appIdList)],
            'placement_id' => ['required', Rule::in($placementIdList)],
            'date_end'     => ['required', 'date_format:Ymd', 'before_or_equal:' . $lastDay],
            'date_start'   => ['required', 'date_format:Ymd', 'before_or_equal:' . $request->get('date_end')],
        ];
        $this->validate($request, $rules);

        $client = new Client([
            'timeout' => 10.0,
        ]);
        $urlParam = [
            'r_type' => 2,
            'publisher_id' => $request->get('publisher_id'),
            'app_id'       => $request->get('app_id'),
            'placement_id' => $request->get('placement_id'),
            'start'        => $request->get('date_start'),
            'date'         => $request->get('date_end'),
        ];

        Log::info('RevenueDeductionSubAccountReUpdate URL Param:', $urlParam);

        try {
            $response = $client->request('GET', env('BI_SERVICE_DEDUCTION_RET'), ['query' => $urlParam]);

            if ($response->getStatusCode() == 200) {
                $tmpData = json_decode($response->getBody(), true);
                if ($tmpData['code'] === 0) {
                    Log::info("request data succ");
                }
            }
        } catch (GuzzleException $e) {
            return $this->jsonResponse($e->getTrace(), 9993, "Guzzle Http Request failed");
        }

        return $this->jsonResponse();
    }


}