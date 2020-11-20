<?php

namespace App\Http\Controllers;

use App\Helpers\Export;
use App\Models\MySql\App;
use App\Models\MySql\Geo;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Unit;
use App\Services\Pagination;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChartReportController extends BaseReportController
{
    const REPORT_TYPE_TREND = 1;
    const REPORT_TYPE_COMPARISION = 2;

    protected $defaultInput = [
        'publisher_id' => '',
        'app_uuid' => '',
        'placement_uuid' => '',
        'scenario_uuid' => '',
        'unit_id' => "",
        'group_id' => "",
        'geo' => [],
        'format' => 'all',
        'nw_firm_id' => 'all',
        'platform' => 'all',
        'sdk_version' => '',
        'app_version' => '',
        'timezone' => '8',
    ];

    protected $metricCfgList = [
        "app_strategy_request" => [
            'default' => false,
            'choose' => false,
            'text' => "App Strategy Request"
        ],
        "placement_strategy_request" => [
            'default' => false,
            'choose' => false,
            'text' => "Placement Strategy Request"
        ],
        "loads" => [
            'default' => true,
            'choose' => true,
            'text' => "Load"
        ],
        "loads_filled_rate" => [
            'default' => true,
            'choose' => true,
            'text' => "Load Filled Rate"
        ],
        "request" => [
            'default' => true,
            'choose' => true,
            'text' => "Request"
        ],
        "request_filled_rate" => [
            'default' => true,
            'choose' => true,
            'text' => "Request Filled Rate"
        ],
        "show" => [
            'default' => true,
            'choose' => true,
            'text' => "Show"
        ],
        "impression" => [
            'default' => true,
            'choose' => true,
            'text' => "Impression"
        ],
        "click" => [
            'default' => true,
            'choose' => true,
            'text' => "Click"
        ],
        "ctr" => [
            'default' => true,
            'choose' => true,
            'text' => "Ctr"
        ]
    ];

    public function index(Request $request)
    {
        /*
         {
        "start_time": 20190823,
        "end_time": 20190830,
        "start":0,
        "limit": 25,
        "area": ["CN"],
        "publisher_id": [22, 34],
        "app_id": [246, 245],
        "placement_id": [335, 666],
        "unit_id": [455],
        "format": "2",
        "nw_firm_id": 1,
        "platform": 2,
        "segment_id": [0],
        "sdk_version": ["3.2.3", "3.2.4"],
        "app_version": ["1.0.7", "1.0.8"]
        }
         */
        $useReportApiV2 = $this->checkRportApiV2($request);

        $commonParam = $this->genCommonParam($request);

        $parseDateRange = self::getIndexDateRange($request, 1);

        // 如果是 Chart Report V2 路由进来的，使用V2的BI接口
        $trendChartReportUrl = $this->getReportApi($request, env('BI_SERVICE_ADMIN_CHART_REPORT')); // env('BI_SERVICE_ADMIN_CHART_REPORT') . ($uri == 'chart-report-v2' ? '/v2' : '');

        $reportType = $request->input('report_type', self::REPORT_TYPE_TREND);
        $serviceParam = array(
            'export' => $request->input("export", 0),
            'report_type' => $reportType,
            'url' => [
                'excel_report' => $trendChartReportUrl,
            ],
            'param' => [
                'date_time' => [
                    intval(date('Ymd', $parseDateRange['time']['start'])),
                    intval(date('Ymd', $parseDateRange['time']['end'])),
                ],
                'date_time_between' => true,
                'common' => $commonParam['bi']
            ]
        );
//        return $serviceParam;
        $pageAppends = $request->all();
        $calendarDateTimeList = [date('Y/m/d', strtotime("-7 day")), date("Y/m/d")];
        if ($reportType == self::REPORT_TYPE_COMPARISION) {
            $serviceParam['param']['date_time'] = [];// reset
            $calendarDateTime = $request->input("calendar_date_time", "");
            if ($calendarDateTime != "") {
                $calendarDateTimeList = json_decode($calendarDateTime);
            }
            foreach ($calendarDateTimeList as $dateTime) {
                $serviceParam['param']['date_time'][] = intval(str_replace("/", "", $dateTime));
            }
            $serviceParam['param']['date_time_between'] = false;
        }

        $reportData = $this->fetchChartReportData($serviceParam, array_keys($this->metricCfgList));
        $report = Pagination::paginate($reportData['counts'], $reportData['excel_report'], $this->perPage, 'page', $request->input("page", 1));

        $searchParam = $commonParam['front'];

        return view('chart-report.index')
            ->with("excelReportData", $reportData['excel_report'])
            ->with("report", $report)
            ->with("trendReportData", $reportData['trend_report'])
            ->with("comparisionReportData", $reportData['comparision_report'])
            ->with('reportType', $reportType)
            ->with('metricsCfg', $this->metricCfgList)
            ->with('publisherId', $searchParam['publisher_id'])
            ->with('appUuid', $searchParam['app_uuid'])
            ->with('placementUuid', $searchParam['placement_uuid'])
            ->with('scenarioUuid', $searchParam['scenario_uuid'])
            ->with('unitId', $searchParam['unit_id'])
            ->with('groupId', $searchParam['group_id'])
            ->with('geo', $searchParam['geo'])
            ->with('geoMap', Geo::getGeoMap())
            ->with('formatMap', Placement::getFormatMap())
            ->with('nwFirmMap', NetworkFirm::getAllIntegratedNwFirmMap())
            ->with('customNwIdNameWithPublisherMap', NetworkFirm::getCustomNwIdNameWithPublisherMap())
            ->with('dateRange', $parseDateRange['string'])
            ->with('calendarDateTimeList', $calendarDateTimeList)
            ->with('format', $searchParam['format'])
            ->with('nwFirmId', $searchParam['nw_firm_id'])
            ->with('platformMap', App::getPlatformMap())
            ->with('systemMap', App::getSystemMap())
            ->with('platform', $searchParam['platform'])
            ->with('sdkVersion', $searchParam['sdk_version'])
            ->with('appVersion', $searchParam['app_version'])
            ->with('timezone', $searchParam['timezone'])
            ->with('pageAppends', $pageAppends)
            ->with('useReportApiV2', $useReportApiV2)
            ->with('timezoneList', $this->timezoneList);
    }

    private function getDefaultMetricList()
    {
        $defaultMetricList = [];
        foreach ($this->metricCfgList as $metric => $cfg) {
            if ($cfg['default']) {
                $defaultMetricList[] = $metric;
            }
        }

        return $defaultMetricList;
    }

    private function fetchChartReportData($param, $customMetricList)
    {
        $reportData = array(
            'counts' => 0,
            'excel_report' => [],
            'trend_report' => [],
            'comparision_report' => []
        );

        $export = $param['export'];
        $reportType = $param['report_type'];
        $paramContainer = $param['param']['common'];
        $dateTimeRange = $param['param']['date_time'];
        $excelReportUrl = $param['url']['excel_report'];
        $paramKey = [
            'start',
            'end',
            'limit',
            'publisher_id',
            'app_id',
            'placement_id',
            'scenario_list',
            'unit_id',
            'format',
            'area',
            'nw_firm_id',
            'platform',
            'segment_id',
            'sdk_version',
            'app_version',
            'timezone',
        ];
        $urlParam = [
            'date_time' => $dateTimeRange,
            'date_time_between' => true
        ];
        foreach ($paramKey as $key) {
            if (isset($paramContainer[$key])) {
                $urlParam[$key] = $paramContainer[$key];
            }
        }
        if ($reportType == self::REPORT_TYPE_COMPARISION) {
            sort($urlParam['date_time']);
            $urlParam['date_time_between'] = false;
        }

//        if($export == 1){
//            $urlParam['start'] = 0;
//            $urlParam['limit'] = 1000;
//        }

        Log::info("request ChartReport urlParam:" . json_encode($urlParam));
        $options = [
            RequestOptions::JSON => $urlParam,
            RequestOptions::TIMEOUT => 60
        ];
        $client = new \GuzzleHttp\Client();
        try {
            $excelReportRes = array(
                'count' => 0,
                'records' => []
            );
            $biRes = $client->post($excelReportUrl, $options);
            if ($biRes->getStatusCode() == 200) {
                $excelReportRes = json_decode($biRes->getBody(), true);
            }
        } catch (RequestException $e) {
            Log::info("request AdminChartReport err:" . $e->getMessage());
        }

        $excelReportData = $excelReportRes['records'];
        if (empty($excelReportData)) {
            return $reportData;
        }

        try {
            if ($export == 1) {
                $header = [
                    'date_time' => 'date_time',
                    'hour' => 'hour',
                ];
                foreach ($customMetricList as $metric) {
                    $header[$metric] = $metric;
                }
                Export::excel($header, $excelReportData, true, $this->timezoneList[$paramContainer['timezone'] - 100] . "_export");
                exit;
            }
        } catch (\Exception $e) {
            Log::info("chartReport Export Error:" . $e->getMessage());
            exit;
        }

        $excelReportDataMap = [];
        foreach ($excelReportData as $index => $item) {
            $hourFormat = sprintf("%02d", $item['hour']);

            $requestFilledRateVal = $this->formatRateField($excelReportData[$index], 'request_filled_rate');
            $excelReportData[$index]['request_filled_rate_val'] = $requestFilledRateVal;
            $item['request_filled_rate_val'] = $requestFilledRateVal;

            $loadFilledRateVal = $this->formatRateField($excelReportData[$index], 'loads_filled_rate');
            $excelReportData[$index]['loads_filled_rate_val'] = $loadFilledRateVal;
            $item['loads_filled_rate_val'] = $loadFilledRateVal;

            $ctrVal = $this->formatRateField($excelReportData[$index], 'ctr');
            $excelReportData[$index]['ctr_val'] = $ctrVal;
            $item['ctr_val'] = $ctrVal;

            $excelReportDataMap[$item['date_time']][$hourFormat] = $item;
        }

        $reportData['counts'] = $excelReportRes['count'];
        $reportData['excel_report'] = $this->pageRecordManual($excelReportData, $urlParam['start'], $urlParam['end']);

        // fill the data
        $dateTimeRangeList = [];
        foreach ($dateTimeRange as $date) {
            $dateTimeRangeList[] = $date;
        }
        if ($reportType == self::REPORT_TYPE_TREND) {
            $dateTimeRangeList = [];//reset
            $dateStart = $dateTimeRange[0];
            $dateEnd = $dateTimeRange[1];
            for ($date = $dateStart; $date <= $dateEnd;) {
                $dateTimeRangeList[] = $date;
                $date = intval(date('Ymd', strtotime("+1 day", strtotime($date))));
            }
        }
        sort($dateTimeRangeList);//order by date asc, hour asc

        foreach ($dateTimeRangeList as $date) {
            if (!isset($excelReportDataMap[$date])) {
                $tmpMetricInHour = [];
                for ($hour = 0; $hour < 24; $hour++) {
                    $hourFormat = sprintf("%02d", $hour);
                    $tmpMetricsList = [];
                    foreach ($customMetricList as $metric) {
                        $tmpMetricsList[$metric] = "";
                    }
                    $tmpMetricInHour[$hourFormat] = $tmpMetricsList;
                    $excelReportDataMap[$date] = $tmpMetricInHour;
                }

                continue;
            }

            $tmpMetricInHour = $excelReportDataMap[$date];
            for ($hour = 0; $hour < 24; $hour++) {
                $hourFormat = sprintf("%02d", $hour);
                if (!isset($tmpMetricInHour[$hourFormat])) {
                    $tmpMetricsList = [];
                    foreach ($customMetricList as $metric) {
                        $tmpMetricsList[$metric] = "";
                    }
                    $excelReportDataMap[$date][$hourFormat] = $tmpMetricsList;
                }
            }
        }

        if ($reportType == self::REPORT_TYPE_COMPARISION) {
            $comparisionReportData = [];
            if (!empty($customMetricList)) {
                $comparisionReportData['metric'] = $customMetricList;
                $comparisionReportData['date_time'] = $dateTimeRangeList;
                $metricHourData = [];
                foreach ($customMetricList as $metric) {
                    foreach ($dateTimeRangeList as $dateTime) {
                        for ($hour = 0; $hour < 24; $hour++) {
                            $hourFormat = sprintf("%02d", $hour);
                            $metricHourData[$metric][$dateTime][] = $excelReportDataMap[$dateTime][$hourFormat][$metric];
                        }
                    }
                }

                $comparisionReportData['hour_data'] = $metricHourData;

                $reportData['comparision_report'] = $comparisionReportData;
            }

        } else if ($reportType == self::REPORT_TYPE_TREND) {
            if (!empty($customMetricList)) {
                $dateTimeHourData = [];
                $fieldData = [];

                $maxDateTime = intval($excelReportData[0]['date_time'] . sprintf("%02d", $excelReportData[0]['hour']));
                $stop = false;
                foreach ($dateTimeRangeList as $dateTime) {
                    if(!$stop){
                        for ($hour = 0; $hour < 24; $hour++) {
                            $hourFormat = sprintf("%02d", $hour);
                            if(intval($dateTime.$hourFormat) > $maxDateTime){
                                $stop = true;
                                break;
                            }
                            $dateTimeHourData[] = $dateTime . "-" . $hourFormat;
                        }
                    }
                }

                foreach ($dateTimeHourData as $dateTimeHour) {
                    list($dateTime, $hourFormat) = explode("-", $dateTimeHour);
                    foreach ($customMetricList as $metric) {
                        $fieldData[$metric][] = $excelReportDataMap[$dateTime][$hourFormat][$metric];
                    }
                }

                $trendReportData = [
                    'date_time_hour' => $dateTimeHourData,
                ];
                foreach ($fieldData as $field => $fieldValList) {
                    $trendReportData[$field] = $fieldValList;
                }

                $reportData['trend_report'] = $trendReportData;
            }
        }

        return $reportData;
    }

    private function pageRecordManual($record, $start, $limit)
    {
        $retRecord = [];

        for ($i = $start; $i < $limit; $i++) {
            if (!isset($record[$i])) {
                break;
            }

            $retRecord[] = $record[$i];
        }

        return $retRecord;
    }

    private function formatRateField(&$item, $field)
    {
        switch ($field) {
            case "request_filled_rate":
            case "loads_filled_rate":
            case "ctr":
                return floatval($item[$field]) * 100 . "%";
        }
    }

    private function genCommonParam(Request $request)
    {
        $page = $request->input("page", 1);
        $pageSize = $this->perPage;

        $start = ($page - 1) * $pageSize;
        $end = $page * $pageSize;
        $biParam = array(
            'start' => $start,
            'limit' => $pageSize,
            'end' => $end
        );

        $frontParam = $this->defaultInput;

        $publisherId = $request->input('publisher_id', $this->defaultInput['publisher_id']);
        if ($publisherId != '') {
            $publisherIdList = $this->getIdInListByField(new Publisher(), 'id', 'id', explode(",", $publisherId));
            $frontParam['publisher_id'] = $publisherId;
            $biParam['publisher_id'] = $publisherIdList;
        }
        $appUuid = $request->input('app_uuid', $this->defaultInput['app_uuid']);
        if ($appUuid != '') {
            $appIdList = $this->getIdInListByField(new App(), 'id', 'uuid', explode(",", $appUuid));
            $frontParam['app_uuid'] = $appUuid;
            $biParam['app_id'] = $appIdList;
        }
        $placementUuid = $request->input('placement_uuid', $this->defaultInput['placement_uuid']);
        if ($placementUuid != '') {
            $placementIdList = $this->getIdInListByField(new Placement(), 'id', 'uuid', explode(",", $placementUuid));
            $frontParam['placement_uuid'] = $placementUuid;
            $biParam['placement_id'] = $placementIdList;
        }
        $scenarioUuid = $request->input('scenario_uuid', $this->defaultInput['scenario_uuid']);
        if ($scenarioUuid != '') {
//            $scenarioUuidList = explode(",", $scenarioUuid);
            $frontParam['scenario_uuid'] = $scenarioUuid;
            $biParam['scenario_list'][]  = $scenarioUuid;
        }
        $unitId = $request->input('unit_id', $this->defaultInput['unit_id']);
        if ($unitId != '') {
            $unitIdList = $this->getIdInListByField(new Unit(), 'id', 'id', explode(",", $unitId));
            $frontParam['unit_id'] = $unitId;
            $biParam['unit_id'] = $unitIdList;
        }
        $groupId = $request->input('group_id', $this->defaultInput['group_id']);
        if ($groupId != '') {
            $segmentIdList = explode(",", $groupId);
            $frontParam['group_id'] = $groupId;
            foreach ($segmentIdList as $sId){
                $biParam['segment_id'][] = intval($sId);
            }
        }
        $geo = $request->input('geo', $this->defaultInput['geo']);
        if ($geo != $this->defaultInput['geo']) {
            $geoShortNameList = $this->getIdInListByField(new Geo(), 'short', 'short', $geo);
            $frontParam['geo'] = $geo;
            $biParam['area'] = $geoShortNameList;
        }
        $format = $request->input('format', $this->defaultInput['format']);
        if ($format != $this->defaultInput['format']) {
            $frontParam['format'] = $format;
            $biParam['format'] = $format;
        }
        $nwFirmId = $request->input('nw_firm_id', $this->defaultInput['nw_firm_id']);
        if ($nwFirmId != $this->defaultInput['nw_firm_id']) {
            $frontParam['nw_firm_id'] = $nwFirmId;
            $biParam['nw_firm_id'] = intval($nwFirmId);
        }
        $platform = $request->input('platform', $this->defaultInput['platform']);
        if ($platform != $this->defaultInput['platform']) {
            $frontParam['platform'] = $platform;
            $biParam['platform'] = intval($platform);
//            $biParam['platform'] = $platform;
        }
        $sdkVersion = $request->input('sdk_version', $this->defaultInput['sdk_version']);
        if ($sdkVersion != '') {
            $sdkVersionList = explode(",", $sdkVersion);
            $frontParam['sdk_version'] = $sdkVersion;
            $biParam['sdk_version'] = $sdkVersionList;
        }
        $appVersion = $request->input('app_version', $this->defaultInput['app_version']);
        if ($appVersion != '') {
            $appVersionList = explode(",", $appVersion);
            $frontParam['app_version'] = $appVersion;
            $biParam['app_version'] = $appVersionList;
        }
        $timezone = (int)$request->input('timezone', $this->defaultInput['timezone']);
        $frontParam['timezone'] = $timezone;
        $biParam['timezone'] = $timezone + 100;


        return [
            'bi' => $biParam,
            'front' => $frontParam,
        ];
    }
}
