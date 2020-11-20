<?php

namespace App\Http\Controllers\Channel;

use App\Helpers\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\MySql\Unit;
use App\Models\MySql\Geo;
use App\Models\MySql\Publisher;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\MGroup;
use App\Models\MySql\MetricsReport;
use App\Models\MySql\MetricsSetting;

use App\Helpers\Format;
use App\Helpers\Export;
use App\Helpers\ArrayUtil;
use App\Helpers\Channel;

use App\Services\CustomPage;
use App\Services\Pagination;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;


class ReportController extends BaseReportController
{
//    protected $geoMap = [];
    protected $defaultInput = [
        'groups'         => ['date_time'],
        'publisher_id'   => '',
        'publisher_name' => '',
        'publisher_type' => 'all',
        'app_uuid'       => '',
        'app_name'       => '',
        'placement_uuid' => '',
        'unit_id'        => "",
        'geo'            => [],
        'tk_sc_type'     => 'all',
        'format'         => 'all',
        'nw_firm_id'     => 'all',
        'tk_platform'    => 'all',
        'tk_system'      => 'all',
        'tk_segment_id'  => '',
        'tk_sdk_version' => '',
        'tk_app_version' => '',
        'timezone'       => '8',
        'channel'        => 'all',
    ];
    private $placementIdMapInfo;
    private $isShow = [];

    const FORMAT_NATIVE = '0';
    const FORMAT_RV = '1';

    private function getPublisherIdList($where)
    {
        $pubIdList = $pubIdByName = $pubIdByType = [];
        $pubIdList1 = $pubIdList2 = $pubIdList3 = [];
        $channel = $where['channel'];

        if ($where['publisher_name']) { //当搜索publisher name时转换为publisher id
            $pubIdByName = $this->getIdListByName(new Publisher(), 'id', 'name', $where['publisher_name'], $channel);
            if ($pubIdByName) {
                foreach ($pubIdByName as $k => $v) {
                    $pubIdList1[] = (int)$v;
                }
            }
        }
        if ($where['publisher_type'] && $where['publisher_type'] != 'all') { //当搜索publisher type时转换为publisher id
            $pubIdByType = $this->getIdListByField(new Publisher(), 'id', 'mode', $where['publisher_type'], $channel);
            if ($pubIdByType) {
                if ($pubIdList2) {
                    foreach ($pubIdByType as $k => $v) {
                        if (!in_array($v, $pubIdList2)) {
                            $pubIdList2[] = (int)$v;
                        }
                    }
                } else {
                    foreach ($pubIdByType as $k => $v) {
                        $pubIdList2[] = (int)$v;
                    }
                }
            }
        }
        if ($where["publisher_id"]) { // 搜索 publisher id
            $publisherIdModel = new Publisher();
            $builder = $publisherIdModel->queryBuilder()->whereIn("id", ArrayUtil::explodeString($where['publisher_id'], ',', 'int'));
            $this->adaptModelQueryBuilder($publisherIdModel, $builder, $channel);
            $publisherList = $builder->get()->toArray();
            if(!empty($publisherList)) {
                foreach ($publisherList as $k => $v){
                    $pubIdList3[] = (int)$v['id'];
                }
            }
        }
        $pubIdList["include1"] = $pubIdList1;
        $pubIdList["include2"] = $pubIdList2;
        $pubIdList["include3"] = $pubIdList3;

        $checkDb = false;
        $publisherModel = new Publisher();
        $pubIdIntersectBuilder = $publisherModel->queryBuilder();
        if(!empty($pubIdList1)){
            $checkDb = true;
            $pubIdIntersectBuilder = $pubIdIntersectBuilder->whereIn("id", $pubIdList1);
        }
        if(!empty($pubIdList2)){
            $checkDb = true;
            $pubIdIntersectBuilder = $pubIdIntersectBuilder->whereIn("id", $pubIdList2);
        }
        if(!empty($pubIdList3)){
            $checkDb = true;
            $pubIdIntersectBuilder = $pubIdIntersectBuilder->whereIn("id", $pubIdList3);
        }

        $publisherIdInterSet = [];
        if($channel === 'all'){
            // 兼容bi DAU返回异常的bug
            if($checkDb){
                $this->adaptModelQueryBuilder($publisherModel, $pubIdIntersectBuilder, $channel);
                $publisherIdList = $pubIdIntersectBuilder->get();
                foreach ($publisherIdList as $item) {
                    $publisherIdInterSet[] = (int)$item["id"];
                }
            }
        }else{
            $this->adaptModelQueryBuilder($publisherModel, $pubIdIntersectBuilder, $channel);
            $publisherIdList = $pubIdIntersectBuilder->get();
            foreach ($publisherIdList as $item) {
                $publisherIdInterSet[] = (int)$item["id"];
            }
        }

        return [
            'all' => $pubIdList,
            'publisher_id_intersect' => $publisherIdInterSet
        ];
    }

    private function getAppIdList($where)
    {
        $appIdList = $appIdByName = $appIdByUUid = [];
        $appIdList1 = $appIdList2 = [];
        if ($where['app_name']) { //当搜索app name时转换为app id
            $appModel = new App();
            $appIdByName = $this->getIdListByName($appModel, 'id', 'name', $where['app_name']);
            if ($appIdByName) {
                foreach ($appIdByName as $k => $v) {
                    $appIdList1[] = (int)$v;
                }
            }
        }

        if ($where['app_uuid']) { //当搜索app uuid时转换为app id
            $appModel = new App();
            $appIdByUUid = $this->getIdListByName($appModel, 'id', 'uuid', $where['app_uuid']);
            if ($appIdByUUid) {
                if ($appIdList2) {
                    foreach ($appIdByUUid as $k => $v) {
                        if (!in_array($v, $appIdList2)) {
                            $appIdList2[] = (int)$v;
                        }
                    }
                } else {
                    foreach ($appIdByUUid as $k => $v) {
                        $appIdList2[] = (int)$v;
                    }
                }
            }
        }
        $appIdList["include1"] = $appIdList1;
        $appIdList["include2"] = $appIdList2;
        return $appIdList;
    }

    private function getAreaIdList($where)
    {
        $areaIdList = [];
        if ($where["geo"]) {
            $areaIdList = $this->getIdListByField(new Geo(), 'id', 'name', $where['geo']);
        }

        return $areaIdList;
    }

    public function index(Request $request)
    {
        $this->checkAccessPermission('full_report_list');

        $roleId    = Channel::getRoleId(Auth::id());
        $channelId = Channel::getChannelId($roleId);

        $useReportApiV2 = $this->checkRportApiV2($request);

        $page = $request->input($this->pageName, 1);

        $parseDateRange = self::getIndexDateRange($request);

        $groupBy = $request->input('groups', $this->defaultInput['groups']);
        $orderByParam = array();
        if($request->has("order_by")){
            $orderBy = $request->input('order_by');
            foreach ($orderBy as $key => $value) {
                $orderByParam[] = [$key => $value];
            }
        } else {
            $orderBy = array("date_time" => "desc");
            $orderByParam = array(
                ['date_time' => 'desc'],
                ['api_revenue' => 'desc'],
                ['sdk_loads' => "desc"],
                ['sdk_impression' => 'desc']
            );
            if (!in_array('date_time', $groupBy)) {
                unset($orderByParam[0]);
                unset($orderBy['date_time']);
                $orderBy['revenue'] = 'desc';
            }
        }

        $publisherId   = $request->input('publisher_id', $this->defaultInput['publisher_id']);
        $publisherName = $request->input('publisher_name', $this->defaultInput['publisher_name']);
        $publisherType = $request->input('publisher_type', $this->defaultInput['publisher_type']);
        $appUuid       = $request->input('app_uuid', $this->defaultInput['app_uuid']);
        $appName       = $request->input('app_name', $this->defaultInput['app_name']);
        $placementUuid = $request->input('placement_uuid', $this->defaultInput['placement_uuid']);
        $unitId        = $request->input('unit_id', $this->defaultInput['unit_id']);
        $geo           = $request->input('geo', $this->defaultInput['geo']);
        $scType        = $request->input('tk_sc_type', $this->defaultInput['tk_sc_type']);
        $format        = $request->input('format', $this->defaultInput['format']);
        $nwFirmId      = $request->input('nw_firm_id', $this->defaultInput['nw_firm_id']);
        $platform      = $request->input('platform', $this->defaultInput['tk_platform']);
        $system        = $request->input('system', $this->defaultInput['tk_system']);
        $segmentId     = $request->input('segment_id', $this->defaultInput['tk_segment_id']);
        $sdkVersion    = $request->input('sdk_version', $this->defaultInput['tk_sdk_version']);
        $appVersion    = $request->input('app_version', $this->defaultInput['tk_sdk_version']);
        $timezone      = $request->input('timezone', $this->defaultInput['timezone']);
        $channel       = $request->input('channel', $this->defaultInput['channel']);
        if($channel != 'all'){
            $channel = (int)$channel;
        }
        $export = $request->input('export', 0);
        if ($export == 1) {
            $this->perPage = 50000;
        }

        $where = [
            'start_date'     => date('Ymd', $parseDateRange['time']['start']),
            'end_date'       => date('Ymd', $parseDateRange['time']['end']),
            'publisher_id'   => $publisherId,
            'publisher_name' => $publisherName,
            'publisher_type' => $publisherType,
            'app_uuid'       => $appUuid,
            'app_name'       => $appName,
            'placement_uuid' => $placementUuid,
            'unit_id'        => intval($unitId),
            'geo'            => $geo,
            'tk_sc_type'     => intval($scType),
            'format'         => $format,
            'nw_firm_id'     => intval($nwFirmId),
            'tk_platform'    => $platform,
            'tk_system'      => $system,
            'tk_sdk_version' => $sdkVersion,
            'tk_app_version' => $appVersion,
            'timezone'       => $timezone,
            'channel'        => $channel,
        ];

        // 排序只能一维排序
        $realOrderBy = array();
        foreach ($orderByParam as $key => $val) {
            foreach($val as $field => $direction){
                if($field == "revenue") {
                    $realOrderBy[] = ['api_revenue' => $direction];
                }
            }
            $realOrderBy[] = $val;
        }

        $platformLocalMap = [
            "all" => 0,
            "1"   => 1,
            "2"   => 2,
        ];
        $systemLocalMap = [
            "all" => 0,
            "1"   => 1,
            "2"   => 2,
        ];
        $formatLocalMap = [
            "all" => null,
            "0"   => "0",
            "1"   => "1",
            "2"   => "2",
            "3"   => "3",
            "4"   => "4",
        ];
        $scTypeLocalMap = [
            "all" => null,
            "0"   => 0,
            "1"   => 1,
            "2"   => 2
        ];

        $list = [
            "list"  => [],
            "total" => 0,
        ];

        $tmpP = CustomPage::getPage($this->perPage, $this->pageName);

        $publisherQuery = Publisher::select('id')
            ->where('channel_id', $channelId);
        if(!empty($publisherId)){
            $publisherQuery->whereIn('id', ArrayUtil::explodeString($publisherId, ',', 'int'));
        }
        if(!empty($publisherName)){
            $publisherQuery->where('name', 'like', "%{$publisherName}%");
        }
        $publisherIds = $publisherQuery->get()
            ->toArray();
        $publisherIds = array_column($publisherIds, 'id');


//        $appIdList = $this->getAppIdList($where);
//        $appIdList['include2'] = [];
//        $appUuids  = ArrayUtil::explodeString($appUuid, ',');
//        if(!empty($appUuids)){
//            $appTmp = App::select('id')
//                ->whereIn('uuid', $appUuids)
//                ->get()
//                ->toArray();
//            if(!empty($appTmp)){
//                $appIdList['include2'] = array_column($appTmp, 'id');
//            }
//        }

        $appQuery = App::select('id');
        if(!empty($appUuids)){
            $appQuery->whereIn('uuid', ArrayUtil::explodeString($appUuid, ','));
        }
        if(!empty($appName)){
            $appQuery->where('name', 'like', "%{$appName}%");
        }
        $appIds = $appQuery->get()
            ->toArray();
        $appIds = array_column($appIds, 'id');


        $placementList  = [];
        $placementUuids = ArrayUtil::explodeString($placementUuid, ',');
        if(!empty($placementUuids)){
            $placementTmp = Placement::select('id')
                ->whereIn('uuid', $placementUuids)
                ->get()
                ->toArray();
            if(!empty($placementTmp)){
                $placementList = array_column($placementTmp, 'id');
            }
        }


        $requestBi = true;
        if(empty($publisherIds)) {
            Log::info("inChannelMode, but_publisher_id_all_empty, will fill it");
            $requestBi = false;

            $publisherModel = new Publisher();
            $publisherIds = $publisherModel->getAllPublisherIdForChannel($channelId);

            Log::info("fill_result:" . json_encode($publisherIds));
            if(!empty($publisherIds)){
                $requestBi = true;
            }
        }

        Log::Info("requestBi_Service:" . $requestBi);

        if($requestBi){
            $urlParam = [
                "start"      => ($tmpP - 1) * $this->perPage,
                "limit"      => $this->perPage,
                "start_time" => intval($where["start_date"]),
                "end_time"   => intval($where["end_date"]),
                "group_by"   => $groupBy,
                "order_by"   => $realOrderBy,
                'timezone'   => $where['timezone'] + 100,

                //多选
                "contains" => (object)[
                    "publisher_id" => $publisherIds,
                    "app_id"       => $appIds,
                    "area"         => $geo,
                    'placement_id' => $placementList,
                    'ad_source_id' => ArrayUtil::explodeString($unitId, ',', 'int'),
                    'segment_ids'  => ArrayUtil::explodeString($segmentId, ',', 'int'),
                    'sdk_version'  => ArrayUtil::explodeString($sdkVersion, ','),
                    'app_version'  => ArrayUtil::explodeString($appVersion, ','),
                ],

                //单选
                "singles" => (object)[
                    "sc_type"    => $scTypeLocalMap[$where["tk_sc_type"]],
                    "format"     => $formatLocalMap[$where["format"]],
                    "nw_firm_id" => $where["nw_firm_id"],
                    "platform"   => $platformLocalMap[$where["tk_platform"]],
                    "system"     => $systemLocalMap[$where["tk_system"]],
                ],
            ];

            $reportApi = $this->getReportApi($request, env("BI_SERVICE_ADMIN_REPORT"));
            Log::info("bi_admin_report_request:" . " url: " . $reportApi . ", param: " . json_encode($urlParam));

            $client = new \GuzzleHttp\Client();
            try {
                $response = $client->post($reportApi, [RequestOptions::JSON => $urlParam]);
                if ($response->getStatusCode() == 200) {
                    $tmpData = json_decode($response->getBody(), true);
                    if ($tmpData['code'] == 0 && $tmpData['data'] && $tmpData['data']['count']) {
                        $list = [
                            "list" => $tmpData['data']['list'],
                            "total" => $tmpData['data']['count'],
                        ];
                    }
                }
            } catch (RequestException $e) {
                Log::info("admin_report_exception:" . json_encode($e->getMessage()));
            }
        }

        $metricsSettingModel = new MetricsSetting();

        $tableFilter = [
            // group by
            'date_time'      => 'Date',
            'publisher_id'   => "Publisher<br/>Id",
            'publisher_type' => "Publisher<br/>Type",
            'publisher_name' => "Publisher<br/>Name",
            'app_uuid'       => "App<br/>UUID",
            'app_name'       => "App<br/>Name",
            'placement_uuid' => "Placement<br/>UUID",
            'placement_name' => "Placement<br/>Name",
            'unit_id'        => 'AD Source<br/>ID',
            'unit_name'      => 'AD Source<br/>Name',
            'geo_short'      => "Area",
            'format_name'    => 'Format',
            'nw_firm_name'   => "Network",
            'segment_id'     => "Segment ID",
            'segment_name'   => 'Segment Name',
            'sdk_version'    => 'SDK<br/>Version',
            'app_version'    => 'APP<br/>Version',
        ];

        $tableFields = [
            // 其他列
            "revenue"                 => "API<br/>Revenue",
            'dau'                     => 'DAU',
            'sdk_loads'               => 'Load',
            'sdk_filled_loads_rate'   => 'Load<br />Filled<br />Rate',
            'sdk_request'             => "SDK<br/>Request",
            'api_request'             => 'API<br/>Request',
            'sdk_filled_request'      => "SDK<br/>Filled<br/>Request",
            'api_filled_request'      => "API<br/>Filled<br/>Request",
            'sdk_filled_request_rate' => "SDK Filled<br/>Request Rate",
            'api_filled_request_rate' => "API Filled<br/>Request Rate",
            'sdk_show'                => "<br/>SDK Show",
            'sdk_impression'          => "SDK<br/>Impression",
            'api_impression'          => "API<br/>Impression",
            'sdk_ecpm'                => 'SDK<br/>eCPM',
            'api_ecpm'                => 'API<br/>eCPM',
            'tk_rv_start'             => "SDK<br/>Video Start",
            'unit_rv_start'           => "API<br/>Video Start",
            'tk_rv_complete'          => "SDK<br/>Video Complete",
            'unit_rv_complete'        => "API<br/>Video Complete",
            'sdk_click'               => "SDK<br/>Click",
            'api_click'               => "API<br/>Click",
            'sdk_ctr'                 => "SDK<br/>CTR",
            'api_ctr'                 => "API<br/>CTR",
            "cost"                    => "Cost<br/>",
            "profit"                  => "Profit<br/>",
        ];


        $metricsSettingFields = $metricsSettingModel->getFullReportSettingFields();
        foreach($tableFields as $key => $val){
            if(!in_array($key, array_keys($metricsSettingFields))){
                unset($tableFields[$key]);
            }
        }

        $tableFields = [
            // group by
            'date_time' => 'Date',
            'publisher_id' => "Publisher<br/>Id",
            'publisher_name' => "Publisher<br/>Name",
            'app_uuid' => "App<br/>UUID",
            'app_name' => "App<br/>Name",
            'placement_uuid' => "Placement<br/>UUID",
            'placement_name' => "Placement<br/>Name",
            'unit_id' => 'AD Source<br/>ID',
            'unit_name' => 'AD Source<br/>Name',
            'geo_short' => "Area",
            'format_name' => 'Format',
            'nw_firm_name' => "Network",

            // 其他列
            "revenue" => "API<br/>Revenue",
            'dau' => 'DAU',/////
            'sdk_loads' => 'Load',
            'sdk_filled_loads_rate' => 'Load<br />Filled<br />Rate',
            'sdk_request' => "SDK<br/>Request",
            'api_request' => 'API<br/>Request',
            'sdk_filled_request' => "SDK<br/>Filled<br/>Request",
            'api_filled_request' => "API<br/>Filled<br/>Request",
            'sdk_filled_request_rate' => "SDK Filled<br/>Request Rate",
            'api_filled_request_rate' => "API Filled<br/>Request Rate",
            'sdk_show' => "<br/>SDK Show",
            'sdk_impression' => "SDK<br/>Impression",
            'api_impression' => "API<br/>Impression",
            'sdk_ecpm' => 'SDK<br/>eCPM',
            'api_ecpm' => 'API<br/>eCPM',
            'tk_rv_start' => "SDK<br/>Video Start",
            'unit_rv_start' => "API<br/>Video Start",
            'tk_rv_complete' => "SDK<br/>Video Complete",
            'unit_rv_complete' => "API<br/>Video Complete",
            'sdk_click' => "SDK<br/>Click",
            'api_click' => "API<br/>Click",
            'sdk_ctr' => "SDK<br/>CTR",
            'api_ctr' => "API<br/>CTR",
        ];
        if($export == 1){
            $tableFields = [
                // group by
                'date_time' => 'Date',
                'publisher_id' => "Publisher<br/>Id",
                'publisher_name' => "Publisher<br/>Name",
                'app_uuid' => "App<br/>UUID",
                'app_name' => "App<br/>Name",
                'package_name' => 'Package<br />Name',
                'bundle_id' => 'Bundle ID',
                'placement_uuid' => "Placement<br/>UUID",
                'placement_name' => "Placement<br/>Name",
                'unit_id' => 'AD Source<br/>ID',
                'unit_name' => 'AD Source<br/>Name',
                'geo_short' => "Area",
                'format_name' => 'Format',
                'nw_firm_name' => "Network",

                // 其他列
                "revenue" => "API<br/>Revenue",
                'dau' => 'DAU',/////
                'sdk_loads' => 'Load',
                'sdk_filled_loads_rate' => 'Load<br />Filled<br />Rate',
                'sdk_request' => "SDK<br/>Request",
                'api_request' => 'API<br/>Request',
                'sdk_filled_request' => "SDK<br/>Filled<br/>Request",
                'api_filled_request' => "API<br/>Filled<br/>Request",
                'sdk_filled_request_rate' => "SDK Filled<br/>Request Rate",
                'api_filled_request_rate' => "API Filled<br/>Request Rate",
                'sdk_show' => "<br/>SDK Show",
                'sdk_impression' => "SDK<br/>Impression",
                'api_impression' => "API<br/>Impression",
                'sdk_ecpm' => 'SDK<br/>eCPM',
                'api_ecpm' => 'API<br/>eCPM',
                'tk_rv_start' => "SDK<br/>Video Start",
                'unit_rv_start' => "API<br/>Video Start",
                'tk_rv_complete' => "SDK<br/>Video Complete",
                'unit_rv_complete' => "API<br/>Video Complete",
                'sdk_click' => "SDK<br/>Click",
                'api_click' => "API<br/>Click",
                'sdk_ctr' => "SDK<br/>CTR",
                'api_ctr' => "API<br/>CTR",
            ];
        }

        $lastLengthKey = 0;
        $tmpLength = 0;
        $appIds = [];
        foreach ($list["list"] as $k => &$v) {
            if (count($v) > $tmpLength) {
                $tmpLength = count($v);
                $lastLengthKey = $k;
            }
            if(in_array('app_id', $groupBy)){
                $appIds[] = $v['app_id'];
            }
            if(isset($v['dau'])){
                $v['dau'] = number_format($v['dau']);
            }
            if(in_array('group_id', $groupBy) && isset($v['segment_info']) && !empty($v['segment_info'])){
                $v['segment_id']   = $v['segment_info']['segment_id'];
                $v['segment_name'] = $v['segment_info']['segment_name'];
                if($v['segment_id'] == 0){
                    $v['segment_name'] = '默认分组';
                }
            }
        }
        // 填充App的Package Name / Bundle ID
        if(!empty($appIds)){
            $appPackage = App::select('id', 'platform_app_id', 'bundle_id', 'platform')
                ->whereIn('id', $appIds)
                ->get()
                ->toArray();
            $appPackageMap  = array_column($appPackage, 'platform_app_id', 'id');
            $appBundleIdMap = array_column($appPackage, 'bundle_id', 'id');
            $appPlatformMap = array_column($appPackage, 'platform', 'id');
            foreach ($list["list"] as $k => &$v) {
                $v['package_name'] = data_get($appPackageMap, $v['app_id'], '');
                $v['package_name'] = empty($v['package_name']) ? '-' : $v['package_name'];
                $v['bundle_id']    = data_get($appBundleIdMap, $v['app_id'], '');
                $v['bundle_id']    = empty($v['bundle_id']) ? '-' : $v['bundle_id'];
                $v['platform']     = data_get($appPlatformMap, $v['app_id'], '');
                $v['platform']     = $v['platform'] == 1 ? 'Android' : 'iOS';
            }
        }

        //Group By Publisher时，显示该Publisher的Publisher Type
        $publisherTypeMap = (new Publisher())->getPublisherTypeMap();

        if ($export == 1) {
            $showFields = [];
            if (!empty($list["list"])) {
                $showFields = array_intersect(array_keys($tableFields), array_keys($list["list"][$lastLengthKey]));
            }

            $header = [];
            foreach ($tableFields as $key => $val) {
                if (in_array($key, $showFields)) {
                    $header[$key] = str_replace('<br/>', ' ', $val);
                }
            }
            $timezoneList = Utils::getTimezoneList();
            Export::excel($header, $list['list'], true, $timezoneList[$timezone] . "_export");
            exit;
        }

        $showFields = [];
        if (!empty($list["list"])) {
            $showFields = array_intersect(array_keys($tableFields), array_keys($list["list"][$lastLengthKey]));
        }

        $total = data_get($list, 'total', 0);

        $report = Pagination::paginate($total, $list['list'], $this->perPage, $this->pageName, $page);

        $metricsSetting = $metricsSettingModel->getFullReportSettings();
        $metricsSettingIds = array_column($metricsSetting, 'metrics_id');


        $sortFields = [
            'date_time',
            'sdk_loads',
            'sdk_show',
            'sdk_request',
            'sdk_filled_request',
            'sdk_impression',
            'sdk_click',
            'sdk_rv_start',
            'sdk_rv_complete',
            'api_request',
            'api_filled_request',
            'api_impression',
            'api_click',
            'api_rv_start',
            'api_rv_complete',
            'revenue',
        ];

        return view('channel.report-full.index')
            ->with('publisherId', $publisherId)
            ->with('publisherName', $publisherName)
            ->with('publisherType', $publisherType)
            ->with('publisherTypeMap', $publisherTypeMap)
            ->with('appUuid', $appUuid)
            ->with('appName', $appName)
            ->with('placementUuid', $placementUuid)
            ->with('unitId', $unitId)
            ->with('geo', $geo)
            ->with('geoMap', self::getGeoMap())
            ->with('scType', $scType)
            ->with('report', $report)
            ->with('dateRange', $parseDateRange['string'])
            ->with('groups', $groupBy)
            ->with('orderBy', $orderBy)
            ->with('scTypeMap', self::getScTypeMap())
            ->with('scType', $scType)
            ->with('formatMap', self::getFormatMap())
            ->with('format', $format)
            ->with('nwFirmMap', self::getNwFirmMap())
            ->with('nwFirmId', $nwFirmId)
            ->with('platformMap', self::getPlatformMap())
            ->with('platform', $platform)
            ->with('system', $system)
            ->with('segmentId', $segmentId)
            ->with('sdkVersion', $sdkVersion)
            ->with('appVersion', $appVersion)
            ->with('timezone', $timezone)
            ->with('tableFilter', $tableFilter)
            ->with('tableFields', $tableFields)
            ->with('showFields', $showFields)
            ->with('sortFields', $sortFields)
            ->with('pageAppends', $request->all())
            ->with('useReportApiV2', $useReportApiV2)
            ->with('timezoneList', Utils::getTimezoneList())
            ->with('channel', $channel)
            ->with('channelMap', self::getChannelMap())
            ->with('metricsFields', (new MetricsReport())->getFullReportFields())
            ->with('metricsSettingIds', $metricsSettingIds);
    }

    private function filledList($list)
    {
        if (!$list) {
            return [];
        }

        $canFilledConfig = [
            'publisher_id' => 'publisher_name', 'app_id' => 'app_name',
            'placement_id' => 'placement_name', 'geo_short' => 'geo_name',
            'sc_type' => 'sc_name', 'nw_firm_id' => 'nw_firm_name',
            'tk_group_id' => 'group_name', 'format' => 'format_name',
            'unit_id' => 'unit_name',
        ];

        $canFilled = array_keys($canFilledConfig);

        $fields = array_keys($list[0]);
        //只要有placement_id，就要展示format
        if (in_array('placement_id', $fields) && !in_array('format', $fields)) {
            $fields[] = 'format';
        }

        $needFilled = array_intersect($canFilled, $fields);

        $needFilledStatus = $needFilled ? true : false;

        $map = [];
        if ($needFilledStatus) {
            $map = $this->getFilledMaps($list, $needFilled);
        }

        $newList = [];

        foreach ($list as $val) {
            //因为format 需要 做map转换，所以放在map转换之前
            if (isset($val['placement_id']) && !isset($val['format'])) {
                $val['format'] = $this->placementIdMapInfo[$val['placement_id']]['format'];
            }

            //map转换
            if ($needFilledStatus) {
                foreach ($needFilled as $fv) {
                    $val[$canFilledConfig[$fv]] = data_get($map[$fv], $val[$fv], '-');
                }
            }

            $unitRevenue = 0;
            if (!$this->isHideField('unit_revenue')) {
                $unitRevenue = data_get($val, 'unit_revenue', 0);
                $val['unit_revenue'] = self::money($unitRevenue);
                //unit_revenue_val 存储 unit_revenue的数字值结果
                if (!$unitRevenue || $unitRevenue == '-') {
                    $val['unit_revenue_val'] = '-';
                } else {
//                    $unitRevenueVal = number_format($unitRevenue, 2);
                    $val['unit_revenue_val'] = floatval(str_replace(',', '', $unitRevenue));//考虑,分隔符
                }
            }//else unit_xxx 全部隐藏，unit_revenue_val也将不存在

            $val['tk_filled_request_rate'] = self::rate(data_get($val, 'tk_filled_request', 0), data_get($val, 'tk_request', 0), 2);

            if (!$this->isHideField('unit_filled_request')
                && !$this->isHideField('unit_request')
                && !$this->isHideField('unit_filled_request_rate')) {
                $val['unit_filled_request_rate'] = self::rate(data_get($val, 'unit_filled_request', 0), data_get($val, 'unit_request', 0), 2);
            }

            $val['tk_ctr'] = self::rate(data_get($val, 'tk_click', 0), data_get($val, 'tk_impression', 0), 2);

            if (!$this->isHideField('unit_click')
                && !$this->isHideField('unit_impression')
                && !$this->isHideField('unit_ctr')) {
                $val['unit_ctr'] = self::rate(data_get($val, 'unit_click', 0), data_get($val, 'unit_impression', 0), 2);
            }

            if (data_get($val, 'tk_impression', 0)) {
                $tkImpressionVal = data_get($val, 'tk_impression', 0) / 1000;
                $val['tk_impression_val'] = $tkImpressionVal;
                $val['tk_ecpm'] = self::money(Format::division($unitRevenue, $tkImpressionVal, 4));
            } else {
                $val['tk_impression_val'] = '-';
                $val['tk_ecpm'] = '-';
            }

            if (data_get($val, 'unit_impression', 0)) {
                $unitImpressionVal = data_get($val, 'unit_impression', 0) / 1000;
                $val['unit_impression_val'] = $unitImpressionVal;
                $val['unit_ecpm'] = self::money(Format::division($unitRevenue, $unitImpressionVal, 4));
            } else {
                $val['unit_ecpm'] = '-';
            }

            $newList[] = $this->filterHideFiled($val);
        }

        return $newList;
    }

    private function getFilledMaps($list, $needFilled)
    {
        $pubIds = $appIds = $placementIds = $nwFirmIds = $groupIds = $unitIds = [];

        foreach ($list as $val) {
            foreach ($needFilled as $nf) {
                if ($nf == 'publisher_id') {
                    $pubIds[] = $val[$nf];
                }
                if ($nf == 'app_id') {
                    $appIds[] = $val[$nf];
                }
                if ($nf == 'placement_id') {
                    $placementIds[] = $val[$nf];
                }
                if ($nf == 'nw_firm_id') {
                    $nwFirmIds[] = $val[$nf];
                }
                if ($nf == 'tk_group_id') {
                    $groupIds[] = $val[$nf];
                }
                if ($nf == 'unit_id') {
                    $unitIds[] = $val[$nf];
                }
            }
        }

        $pubIdMap = $appIdMap = $placementIdMap = $nwFirmIdMap = $groupIdMap = $unitIdMap = [];

        if ($pubIds) {
            $pubModel = new Publisher();
            $pubIdMap = $this->getMap($pubModel, 'id', 'name', $pubIds);
        }
        if ($appIds) {
            $appModel = new App();
            $appIdMap = $this->getMap($appModel, 'id', 'name', $appIds);
        }
        if ($placementIds) {
            $placementModel = new Placement();
            $this->placementIdMapInfo = $this->getMapInfo($placementModel, 'id', ['name', 'format'], $placementIds);
            $placementIdMap = array_column($this->placementIdMapInfo, 'name', 'id');
        }
        if ($nwFirmIds) {
            $networkFirmModel = new NetworkFirm();
            $nwFirmIdMap = $this->getMap($networkFirmModel, 'id', 'name', $nwFirmIds);
        }
        if ($groupIds) {
            $groupModel = new MGroup();
            $groupIdMap = $this->getMap($groupModel, 'id', 'name', $groupIds);
        }
        if ($unitIds) {
            $unitModel = new Unit();
            $unitIdMap = $this->getMap($unitModel, 'id', 'name', $unitIds);
        }

        return $map = [
            'publisher_id' => $pubIdMap, 'app_id' => $appIdMap,
            'placement_id' => $placementIdMap, 'geo_short' => $this->geoMap,
            'sc_type' => self::getScTypeMap(), 'nw_firm_id' => $nwFirmIdMap,
            'tk_group_id' => $groupIdMap, 'format' => self::getFormatMap(),
            'unit_id' => $unitIdMap
        ];
    }

    private function showDau($groupBy, $where)
    {
        if (!in_array(implode(',', $groupBy), ['date_time', 'date_time,app_uuid'])) {
            return false;
        }

        $showDau = true;

        $tmpWhere = $where;
        unset($tmpWhere['start_date']);
        unset($tmpWhere['end_date']);

        if (implode(',', $groupBy) == 'date_time,app_uuid') {
            unset($tmpWhere['app_uuid']);
            unset($tmpWhere['app_name']);
        }

        foreach ($tmpWhere as $k => $v) {
            if ($v != $this->defaultInput[$k]) {
                $showDau = false;
                break;
            }
        }

        return $showDau;
    }

    private function hideUnitFields()
    {
        $unitField = [
            'unit_request',
            'unit_filled_request',
            'unit_impression',
            'unit_click',
            'unit_revenue',
            'unit_filled_request_rate',
            'unit_ctr',
            'unit_ecpm',
        ];
        foreach ($unitField as $field) {
            $this->isShow[$field] = false;
        }
    }

    /**
     * 去掉需要隐藏的列
     * @param $value
     * @return mixed
     */
    private function filterHideFiled($value)
    {
        $hideField = $this->getHideFiled();
        foreach ($hideField as $field) {
            if (isset($value[$field])) {
                unset($value[$field]);
            }
        }
        return $value;
    }

    /**
     * 获取需要隐藏的列
     * @return array
     */
    private function getHideFiled()
    {
        $hideField = [];
        foreach ($this->isShow as $field => $isShow) {
            if (!$isShow) {
                $hideField[] = $field;
            }
        }
        return $hideField;
    }

    /**
     * 该列是否是隐藏列
     * @param $field
     * @return boolean
     */
    private function isHideField($field)
    {
        $hideField = $this->getHideFiled();
        return in_array($field, $hideField) ? true : false;
    }

    private function getMap($model, $idField, $nameField, $idList)
    {
        return array_column(
            $model->queryBuilder()
                ->select([$idField, $nameField])->whereIn($idField, $idList)
                ->get()->toArray(), $nameField, $idField);
    }

    private function getMapInfo($model, $idField, $fields, $idList)
    {
        $selectFields = $fields;
        $selectFields[] = $idField;

        return array_column(
            $model->queryBuilder()
                ->select($selectFields)->whereIn($idField, $idList)
                ->get()->toArray(), null, $idField);
    }

    private static function money($money, $points = 2)
    {
        if (!$money || $money == '-') {
            $result = '-';
        } else {
            $result = '<span style="color:#e4393c;">';
            if ($money < 0) $result .= '- $' . number_format(-1 * $money, 2);
            else $result .= '$' . number_format($money, $points);
            $result .= '</span>';

            if (number_format($money, $points) == '0.00') $result = '-';
        }

        return $result;
    }

    private static function rate($install, $click, $type = 1, $overflow = false)
    {
        if ($install == '-' || $click == '-') return '-';

        $install = strtr($install, array(',' => ''));
        $click = strtr($click, array(',' => ''));
        $result = $click ? round($install / $click * 100, 2) : 0;
        if ($overflow && $result > 100) $result = $overflow;
        if ($result >= 50 && $type == 1) {
            $result = '<span class="red">' . $result . '%</span>';
        } else if ($result) {
            $result .= '%';
        } else {
            $result = '-';
        }
        return $result;
    }
}
