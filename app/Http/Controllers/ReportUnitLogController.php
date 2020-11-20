<?php

namespace App\Http\Controllers;

use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\ReportUnitLog;
use App\Models\MySql\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ReportUnitLogController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = ReportUnitLog::query()
            ->from(ReportUnitLog::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE. ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t3', 't3.id', '=', 't1.nw_firm_id')
            ->leftJoin(Users::TABLE . ' as t4', 't4.id', '=', 't1.admin_user')
            ->select([
                't1.id', 't1.type', 't1.publisher_id', 't2.name as publisher_name',
                't1.nw_firm_id', 't3.name as nw_firm_name', 't1.pull_start_time', 't1.pull_end_time',
                't1.pull_type', 't1.machine_num', 't1.sdate as pull_date', 't1.utime as update_time',
                't1.status', 't1.admin_user as admin_id', 't4.name as admin_name'])
            ->orderByDesc('id');

        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if (array_key_exists($request->query('type', 0), ReportUnitLog::getTypeMap())) {
            $query->where('t1.type', $request->query('type'));
        }
        if (array_key_exists($request->query('nw_firm_id', -1), ReportUnitLog::getNwFirmMap())) {
            $query->where('t1.nw_firm_id', $request->query('nw_firm_id'));
        }
        if (array_key_exists($request->query('status', -1), ReportUnitLog::getStatusMap())) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 25), ['*'], 'page_no', $request->query('page_no', 1));

        $res = $this->parseResByPaginator($paginator);

        foreach ($res['list'] as $idx => $datum) {
            $res['list'][$idx]['nw_firm_name']   = $datum['nw_firm_name'] ?? '全部平台';
            $res['list'][$idx]['publisher_name'] = $datum['publisher_name'] ?? '所有';
            $res['list'][$idx]['admin_name']     = $datum['admin_name'] ?? '';
        }

        return $this->jsonResponse($res);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {

        $publisherIdList = Publisher::getAllPublisherId();
        $publisherIdList[] = 0;
        $nwFirmIdList = array_keys(NetworkFirm::getAllNwFirmMap());
        $nwFirmIdList[] = 0;

        $rule = [
            'publisher_id_list'   => ['required', 'array'],
            'publisher_id_list.*' => [Rule::in($publisherIdList)],
            'nw_firm_id'          => ['required', Rule::in($nwFirmIdList)],
            'pull_type_list'      => ['required', 'array'],
            'pull_type_list.*'    => ['in:1,2'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['required', 'date', 'after_or_equal:start_date']
        ];

        $this->validate($request, $rule);

        /*  所有记录都相同的属性*/
        $data = [
            'nw_firm_id'      => $request->get('nw_firm_id'),
            'pull_start_time' => date('Y-m-d H:i:s'),         // 拉取开始时间
            'pull_end_time'   => date('Y-m-d H:i:s'),         // 拉取结束时间
            'ctime'           => date('Y-m-d H:i:s'),         // 创建时间
            'utime'           => date('Y-m-d H:i:s'),         // 更新时间
            'status'          => ReportUnitLog::STATUS_PENDING,
            'admin_user'      => Auth::id()
        ];

        $nwFirmId = $request->get('nw_firm_id');
        $publisherIdList = $request->get('publisher_id_list');
        $pullTypeList = $request->get('pull_type_list');
        $begDate = strtotime($request->get('start_date'));
        $endDate = strtotime($request->get('end_date'));
        $hourNwFirmIds = NetworkFirm::query()->where('crawl_support_hour', NetworkFirm::CRAWL_SUPPORT_HOUR_YES)->get()->getQueueableIds();
        $dayNwFirmIds = NetworkFirm::query()->where('crawl_support', NetworkFirm::CRAWL_SUPPORT_DAY_YES)->get()->getQueueableIds();
        $hourNwFirmIds[] = 0;
        $dayNwFirmIds[] = 0;

        /* 按日持久化数据并获取所有新记录的 ID */
        $query = ReportUnitLog::query();
        $insertMap = [];

        /* 每条记录的 sdate 和 edate 相同，因此为整个 sdate 到 edate 时间段内每天都创建一条记录 */
        for ($timeSecond = $begDate; $timeSecond <= $endDate; $timeSecond += 60 * 60 * 24) {
            $data['sdate'] = date('Ymd', $timeSecond);
            $data['edate'] = date('Ymd', $timeSecond);

            /* 为每个开发者创建一条记录 */
            foreach ($publisherIdList as $publisherId) {
                $data['publisher_id'] = $publisherId;

                /* 为每个时间维度创建一条记录 */
                foreach ($pullTypeList as $pullType) {

                    /* 如果该厂商不支持该时间维度，那么直接跳过，厂商默认都支持天维度 */
                    if ($pullType == ReportUnitLog::PULL_TYPE_HOUR && !in_array($nwFirmId, $hourNwFirmIds, false)) {
                        continue;
                    }
                    if ($pullType == ReportUnitLog::PULL_TYPE_DAY && !in_array($nwFirmId, $dayNwFirmIds, false)) {
                        continue;
                    }

                    $data['pull_type'] = $pullType;
                    $id = $query->insertGetId($data);
                    $insertMap[$id] = [
                        'date'         => date('Ymd', $timeSecond),
                        'pull_type'    => $pullType,
                        'publisher_id' => $publisherId
                    ];
                }
            }
        }

        /* 如果没有这个方法，就定义这个方法 */
        if (!function_exists('fastcgi_finish_request')) {
            function fastcgi_finish_request()  {
                return $this->jsonResponse([], 1);
            }
        }

        fastcgi_finish_request();
        ignore_user_abort(true);

        foreach ($insertMap as $actionId => $actionData) {
            $url = 'http://10.29.1.222:6190/report?';
            $param = http_build_query(array(
                'nw_firm_id'   => $nwFirmId,
                'action_id'    => (int)$actionId,
                'date'         => (int)$actionData['date'],
                'pull_type'    => (int)$actionData['pull_type'],
                'publisher_id' => (int)$actionData['publisher_id'],
            ));
            $cmd = 'curl' . ' ' . "'" .$url . $param . "'";
            Log::info('cmd: ' . $cmd);
            $res = exec($cmd);
            Log::info('response: ' . $res);
        }

//        foreach ($publisherIdList as $publisher_id) {
//            foreach ($insertMap as $actionId => $pullType) {
//                for ($i = $sdate; $i <= $edate; $i++) {
//                    $url = 'http://127.0.0.1:6190/report?';
//                    $param = http_build_query(array(
//                        'action_id'    => (int)$actionId,
//                        'date'         => (int)$i,
//                        'pull_type'    => (int)$pullType,
//                        'nw_firm_id'   => (int)$input['nw_firm_id'],
//                        'publisher_id' => (int)$publisher_id,
//                    ));
//                    $res[] = $url . $param;
//                    $cmd = 'curl' . ' ' . "'" .$url . $param . "'";
//                    Log::info('cmd: ' . $cmd);
//                    $res = exec($cmd);
//                    Log::info('response: ' . $res);
//                }
//            }
//        }
    }
}