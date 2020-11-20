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
use App\Models\MySql\ReportBlackAssignmentActualLog;
use App\Models\MySql\Users;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class RevenueDeductionController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = ReportBlackAssignment::query()
            ->from(ReportBlackAssignment::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->leftJoin(App::TABLE . ' as t3', 't3.id', '=', 't2.app_id')
            ->leftJoin(Publisher::TABLE . ' as t4', 't4.id', '=', 't2.publisher_id')
            ->leftJoin(Users::TABLE . ' as t5', 't5.id', '=', 't1.admin_user')
            ->select([
                't1.id', 't4.id as publisher_id', 't4.name as publisher_name',
                't3.id as app_id', 't3.uuid as app_uuid', 't3.name as app_name',
                't2.id as placement_id', 't2.uuid as placement_uuid', 't2.name as placement_name',
                't1.assignment_type', 't1.expected_value', 't1.random_range', 't1.status',
                't1.utime as update_time', 't5.name as admin_name'
            ])->where('t1.type', ReportBlackAssignment::TYPE_MAIN)
            ->orderByDesc('t1.id');

        if ($request->has('publisher_id')) {
            $query->where('t4.id', $request->query('publisher_id'));
        }
        if ($request->has('publisher_name')) {
            $query->where('t4.name', 'like', '%' . $request->query('publisher_name') . '%');
        }
        if ($request->has('app_id')) {
            $tmp = $request->query('app_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            });
        }
        if ($request->has('app_name')) {
            $query->where('t3.name', 'like', '%' . $request->query('app_name') . '%');
        }
        if ($request->has('placement_id')) {
            $tmp = $request->query('placement_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t2.id', $tmp)->orWhere('t2.uuid', $tmp);
            });
        }
        if ($request->has('placement_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('placement_name') . '%');
        }
        if (array_key_exists($request->query('assignment_type', -1), ReportBlackAssignment::getAssignmentTypeMap())) {
            $query->where('t1.assignment_type', $request->query('assignment_type'));
        }
        if (array_key_exists($request->query('status', -1), ReportBlackAssignment::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            /* 仅有一条 */
            if ($datum['placement_id'] == 0) {
                $data['list'][$idx]['placement_uuid'] = '';
                $data['list'][$idx]['placement_name'] = '';
                $data['list'][$idx]['app_uuid']       = '';
                $data['list'][$idx]['app_name']       = '';
                $data['list'][$idx]['publisher_id']   = '';
                $data['list'][$idx]['publisher_name'] = '';
                $data['list'][$idx]['actual_value']   = 0;
                continue;
            }

            /* 补 actual_value，需要在 log 表中查询，由于某些 placement 可能有数万条记录，因此只能一个一个查 */
            $actualLog = ReportBlackAssignmentActualLog::query()
                ->where('country', '00')
                ->where('placement_id', $datum['placement_id'])
                ->orderByDesc('id')
                ->value('actual_log');
            $actualLog = $actualLog == null ? ['actual_value' => 0] : json_decode($actualLog, true);
            /* 默认是小数来的，需要乘以 100 */
            $data['list'][$idx]['actual_value']   = $actualLog['actual_value'] == null ? 0.00 : number_format($actualLog['actual_value'] * 100, 2);
            $data['list'][$idx]['admin_name']     = $datum['admin_name'] ?? '-';
            $data['list'][$idx]['expected_value'] = $datum['assignment_type'] == ReportBlackAssignment::TYPE_ASSIGNMENT_ECPM ? $datum['expected_value'] / 1000 : $datum['expected_value'];
        }

        return $this->jsonResponse($data);
    }

    public function show($id): JsonResponse
    {
        $record = ReportBlackAssignment::query()
            ->from(ReportBlackAssignment::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->select([
                't1.id', 't2.uuid as placement_uuid', 't2.publisher_id',
                't1.assignment_type', 't1.expected_value',
                't1.random_range', 't1.maximum_compensation', 't1.status'
            ])
            ->where('t1.id', $id)
            ->where('t1.type', ReportBlackAssignment::TYPE_MAIN)
            ->firstOrFail();

        if ($record['assignment_type'] == ReportBlackAssignment::TYPE_ASSIGNMENT_ECPM) {
            $record['expected_value'] /= 1000;
        }

        return $this->jsonResponse($record);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'placement_uuid'       => ['required', 'exists:placement,uuid'],
            'assignment_type'      => ['required', Rule::in(array_keys(ReportBlackAssignment::getAssignmentTypeMap()))],
            'expected_value'       => ['required', 'numeric', 'max:1000'],
            'random_range'         => ['required', 'integer', 'min:0', 'max:20'],
            'maximum_compensation' => ['required', 'integer', 'min:0', 'max:10000'],
            'status'               => ['required', Rule::in(array_keys(ReportBlackAssignment::getStatusMap(true)))],
        ];

        $this->validate($request, $rules);

        $assignmentType = $request->get('assignment_type');
        $expectedValue  = $request->get('expected_value');
        $placementId    = Placement::query()->where('uuid', $request->get('placement_uuid'))->value('id');

        /* 根据 Type 类型不同对 expected_value 再做一波校验 */
        if ($assignmentType == ReportBlackAssignment::TYPE_ASSIGNMENT_DISCOUNT) {
            $this->validate($request, ['expected_value' => 'integer|min:0']);
        } else if ($assignmentType == ReportBlackAssignment::TYPE_ASSIGNMENT_ECPM) {
            $this->validate($request, ['expected_value' => 'min:0.001']);
            /* 针对于 assigment_type = 2 的时候 保存是 *1000，即 1 ~ 1,00000 */
            $expectedValue *= 1000;
        }

        /* 不可以创建相同 PlacementId 的记录 */
        if (ReportBlackAssignment::query()->where('placement_id', $placementId)->exists()) {
            return $this->jsonResponse(['placement_uuid' => 'Record already exists!'], 10000, "该 Placement 记录已存在");
        }

        ReportBlackAssignment::query()->insert([
            'type'                 => ReportBlackAssignment::TYPE_MAIN,
            'publisher_id'         => 0,
            'app_id'               => 0,
            'placement_id'         => $placementId,
            'country'              => '00',
            'assignment_type'      => $assignmentType,
            'expected_value'       => $expectedValue,
            'random_range'         => $request->get('random_range'),
            'maximum_compensation' => $request->get('maximum_compensation'),
            'status'               => $request->get('status'),
            'admin_user'           => auth('api')->id(),
            'ctime'                => date('Y-m-d H:i:s'),
            'utime'                => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonResponse([], 1);
    }

    public function update(Request $request, $id):JsonResponse
    {
        $rules = [
            'assignment_type'      => ['required', Rule::in(array_keys(ReportBlackAssignment::getAssignmentTypeMap()))],
            'expected_value'       => ['required', 'numeric', 'max:1000'],
            'random_range'         => ['required', 'integer', 'min:0', 'max:20'],
            'maximum_compensation' => ['required', 'integer', 'min:0', 'max:10000'],
            'status'               => ['required', Rule::in(array_keys(ReportBlackAssignment::getStatusMap(true)))],
        ];

        $this->validate($request, $rules);

        $assignmentType = $request->get('assignment_type');
        $expectedValue  = $request->get('expected_value');

        /* 根据 Type 类型不同对 expected_value 再做一波校验 */
        if ($assignmentType == ReportBlackAssignment::TYPE_ASSIGNMENT_DISCOUNT) {
            $this->validate($request, ['expected_value' => 'integer|min:0']);
        } else if ($assignmentType == ReportBlackAssignment::TYPE_ASSIGNMENT_ECPM) {
            $this->validate($request, ['expected_value' => 'min:0.001']);
            /* 针对于 assigment_type = 2 的时候 保存是 *1000，即 1 ~ 1,00000 */
            $expectedValue *= 1000;
        }

        /* 这样写找不到导致更新失败会提醒一下，不要省代码 */
        $record = ReportBlackAssignment::query()->where('id', $id)
            ->where('type', ReportBlackAssignment::TYPE_MAIN)
            ->firstOrFail();

        $record->update([
            'assignment_type'      => $assignmentType,
            'expected_value'       => $expectedValue,
            'random_range'         => $request->get('random_range'),
            'maximum_compensation' => $request->get('maximum_compensation'),
            'status'               => $request->get('status'),
            'utime'                => date('Y-m-d H:i:s'),
            'admin_user'           => auth('api')->id(),
        ]);

        return $this->jsonResponse();
    }

    public function rerun(Request $request, $id): JsonResponse
    {
        $lastDay = date('Ymd', time() - 24 * 60 * 60 * 2);
        $this->validate($request, ['date' => 'required|date_format:Ymd|before_or_equal:' . $lastDay]);

        $record = ReportBlackAssignment::query()
            ->from(ReportBlackAssignment::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->select(['t1.placement_id', 't2.publisher_id'])
            ->where('t1.type', ReportBlackAssignment::TYPE_MAIN)
            ->where('t1.id', $id)
            ->firstOrFail();

        $client = new \GuzzleHttp\Client([
            'timeout' => 10.0,
        ]);
        $urlParam = [
            'publisher_id' => $record['publisher_id'],
            'placement_id' => $record['placement_id'],
            'date'         => $request->get('date'),
        ];

        try {
            $response = $client->request('GET', env('BI_SERVICE_DEDUCTION_RET'), ['query' => $urlParam]);

            if ($response->getStatusCode() == 200) {
                $resData = json_decode($response->getBody(), true);
                Log::info($this->getControllerUses() . $resData['code'] == 0 ? ": ReRun success" : ": ReRun failed");
                return $this->jsonResponse($resData);
            }
        } catch (GuzzleException $e) {
            Log::info($this->getControllerUses() . ': ReRun BI request failed');
            return $this->jsonResponse([], 9993);
        }
//        $cmd = $res = "";
//        if (env("APP_ENV") == 'local') {
//            $cmd = "php /data/spserver/web/admin.uparpu.com/artisan tool:deal_black_revenue " . $day . " " . $publisherId . " " . $placementId;
//            $res = exec($cmd);
//        } else if (env("APP_ENV") == 'test') {
//            $cmd = "/data/spserver/php/bin/php /home/zengzhihai/server/php/up_server_admin/artisan tool:deal_black_revenue " . $day . " " . $publisherId . " " . $placementId;
//            $res = exec($cmd);
//        } else if (env("APP_ENV") == 'production') {
//            $cmd = "/data/spserver/php/bin/php /data/spserver/web/admin.uparpu.com/artisan tool:deal_black_revenue " . $day . " " . $publisherId . " " . $placementId;
//            $res = exec($cmd);
//        }

//        Log::info($cmd);
//        Log::info($res);

        return $this->jsonResponse();
    }


}