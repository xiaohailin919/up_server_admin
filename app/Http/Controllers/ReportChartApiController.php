<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Helpers\Export;
use App\Helpers\Utils;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\Segment;
use App\Models\MySql\Unit;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\Format;

class ReportChartApiController extends ApiController
{
    const REPORT_TYPE_TREND       = 1;
    const REPORT_TYPE_COMPARISION = 2;
    
    protected $defaultInput = [
        'publisher_id'      => '',
        'app_uuid'          => '',
        'placement_uuid'    => '',
        'scenario_uuid'     => '',
        'unit_id'           => "",
        'group_id'          => "",
        'area'              => [],
        'format'            => '-1',
        'nw_firm_id'        => '',
        'platform'          => '0',
        'sdk_version'       => '',
        'app_version'       => '',
        'timezone'          => '8',
    ];
    
    protected $metricCfgList = [
        "app_strategy_request" => [
            'default' => false,
            'choose'  => false,
            'text'    => "App Strategy Request"
        ],
        "placement_strategy_request" => [
            'default' => false,
            'choose'  => false,
            'text'    => "Placement Strategy Request"
        ],
        "loads" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Load"
        ],
        "loads_filled_rate" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Load Filled Rate"
        ],
        "request" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Request"
        ],
        "request_filled_rate" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Request Filled Rate"
        ],
        "show" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Show"
        ],
        "impression" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Impression"
        ],
        "click" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Click"
        ],
        "ctr" => [
            'default' => true,
            'choose'  => true,
            'text'    => "Ctr"
        ]
    ];

    
    /**
     * 分时报表
     * 请求参数demo:
     {
        "page_no":"1",
        "page_size":"400",
        "report_type":"2",
        "export":"0",
        "timezone":"8",
        "daterange":"2020/10/09 - 2020/10/17",
        "publisher_id":"22",
        "app_uuid":"",
        "placement_uuid":"",
        "scenario_uuid":"",
        "format":"",
        "unit_id":"",
        "platform":"",
        "area":[],
        "group_id":"",
        "sdk_version":"",
        "app_version":"",
        "calendar_date_time":"["2020/11/19","2020/11/02","2020/10/26"]"
     }
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //历史遗留问题：api没启用ConvertEmptyStringsToNull中间件 需将空字符串转换为null 否则请求bi会错误
        foreach($request->all() as $key=>$val){
            $request->offsetSet($key,is_string($val) && $val === '' ? null : $val);
        }
        
        //导出暂时方案
        if(stripos($request->url(),'/export')!==false){
            $request->offsetSet('export',1);
        }
        $pageNo     = $request->input("page_no", 1);
        $pageSize   = $request->input('page_size',20);
        $reportType = $request->input('report_type', self::REPORT_TYPE_TREND);
        $export     = $request->input('export',0);
        $calendarDateTime = $request->input("calendar_date_time", "");

        $commonParam = $this->genCommonParam($request);
        
        if($commonParam === false){
            $resData = [
                'total'                   => 0,
                'page_no'                 => (int)$pageNo,
                'page_size'               => (int)$pageSize,
                'header'                  => array_keys(self::getHeaders()),
                'list'                    => [],
                'trend_report_data'       => (object)[],
                'comparision_report_data' => (object)[]
            ];
            return $this->jsonResponse($resData);
        }
       
        $parseDateRange = self::getIndexDateRange($request, 1);

        // 如果是 Chart Report V2 路由进来的，使用V2的BI接口
        $trendChartReportUrl = env('BI_SERVICE_ADMIN_CHART_REPORT');

        $serviceParam = [
            'export' => $export,
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
        ];

        // return $serviceParam;
        //$pageAppends = $request->all();
        if ($reportType == self::REPORT_TYPE_COMPARISION) {
            if ($calendarDateTime != "") {
                $calendarDateTimeList = is_string($calendarDateTime) ? json_decode($calendarDateTime) : $calendarDateTime;
            } else {
                $calendarDateTimeList = [date('Y/m/d', strtotime("-7 day")), date("Y/m/d")];
            }
            $serviceParam['param']['date_time'] = [];// reset
            foreach ((array)$calendarDateTimeList as $dateTime) {
                $serviceParam['param']['date_time'][] = intval(str_replace("/", "", $dateTime));
            }
            $serviceParam['param']['date_time_between'] = false;
        }

        $reportData = $this->fetchChartReportData($serviceParam, array_keys($this->metricCfgList));
        //$report = Pagination::paginate($reportData['counts'], $reportData['excel_report'], $pageSize, 'page', $pageNo);
        $list = array_slice($reportData['excel_report'], ($pageNo - 1) * $pageSize, $pageSize);
    
        $resData = [
            'total'                   => (int)$reportData['counts'],
            'page_no'                 => (int)$pageNo,
            'page_size'               => (int)$pageSize,
            'header'                  => array_keys(self::getHeaders()),
            'list'                    => $list,
            'trend_report_data'       => (object)$reportData['trend_report'],
            'comparision_report_data' => (object)$reportData['comparision_report']
        ];
        
        return $this->jsonResponse($resData);
    }
    
    /**
     * 格式化输入参数
     * @param Request $request
     * @return array
     */
    private function genCommonParam(Request $request)
    {
        $pageNo         = $request->input("page_no", 1);
        $pageSize       = $request->input('page_size',20);
        
        $publisherId    = $request->input('publisher_id', $this->defaultInput['publisher_id']);
        $appUuid        = $request->input('app_uuid', $this->defaultInput['app_uuid']);
        $placementUuid  = $request->input('placement_uuid', $this->defaultInput['placement_uuid']);
        $scenarioUuid   = $request->input('scenario_uuid', $this->defaultInput['scenario_uuid']);
        $unitId         = $request->input('unit_id', $this->defaultInput['unit_id']);
        $groupId        = $request->input('group_id', $this->defaultInput['group_id']);
        $area           = $request->input('area', $this->defaultInput['area']);
        $format         = $request->input('format', $this->defaultInput['format']);
        $nwFirmId       = $request->input('nw_firm_id', $this->defaultInput['nw_firm_id']);
        $platform       = $request->input('platform', $this->defaultInput['platform']);
        $sdkVersion     = $request->input('sdk_version', $this->defaultInput['sdk_version']);
        $appVersion     = $request->input('app_version', $this->defaultInput['app_version']);
        $timezone       = (int)$request->input('timezone', $this->defaultInput['timezone']);

        $start = ($pageNo - 1) * $pageSize;
        $end   = $pageNo * $pageSize;
        $biParam = [
            'start' => (int)$start,
            'limit' => (int)$pageSize,
            'end'   => (int)$end
        ];
        
        if ($publisherId != '') {
            $biParam['publisher_id'] = [intval($publisherId)];
        }
        if ($appUuid != '') {
            $appIdList = App::query()->where('id',$appUuid)->orWhere('uuid',$appUuid)->pluck('id')->toArray();
            if(empty($appIdList)){
                return false;
            }
            $biParam['app_id'] = $appIdList;
        }
        if ($placementUuid != '') {
            $placementIdList = Placement::query()->where('id',$placementUuid)->orWhere('uuid',$placementUuid)->pluck('id')->toArray();
            if(empty($placementIdList)){
                return false;
            }
            $biParam['placement_id'] = $placementIdList;
        }
        if ($scenarioUuid != '') {
            $biParam['scenario_list']  = [$scenarioUuid];
        }
        if ($unitId != '') {
            $unitIdList = Unit::query()->where('id',$unitId)->pluck('id')->toArray();
            if(empty($unitIdList)){
                return false;
            }
            $biParam['unit_id'] = $unitIdList;
        }
        if ($groupId != '') {
            $segmentIdList = Segment::query()->where('id',$groupId)->pluck('id')->toArray();
            if(empty($segmentIdList)){
                return false;
            }
            $biParam['segment_id'] = $segmentIdList;
        }
        if (!empty($area) && is_array($area)) {
            $biParam['area'] = $area;
        }
        if ($format != $this->defaultInput['format']) {
            $biParam['format'] = strval($format);
        }
        if ($nwFirmId != $this->defaultInput['nw_firm_id']) {
            $biParam['nw_firm_id'] = intval($nwFirmId);
        }
        if ($platform != $this->defaultInput['platform'] && $platform!='') {
            $biParam['platform'] = intval($platform);
        }
        if ($sdkVersion != '') {
            $sdkVersionList = explode(",", $sdkVersion);
            $biParam['sdk_version'] = $sdkVersionList;
        }
        if ($appVersion != '') {
            $appVersionList = explode(",", $appVersion);
            $biParam['app_version'] = $appVersionList;
        }
        $biParam['timezone'] = $timezone + 100;

        return [
            'bi' => $biParam
        ];
    }
    
    
    private function fetchChartReportData($param, $customMetricList)
    {
        $reportData = [
            'counts'             => 0,
            'excel_report'       => [],
            'trend_report'       => [],
            'comparision_report' => []
        ];

        $export         = $param['export'];
        $reportType     = $param['report_type'];
        $paramContainer = $param['param']['common'];
        $dateTimeRange  = $param['param']['date_time'];
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
            'date_time'         => $dateTimeRange,
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
            RequestOptions::JSON    => $urlParam,
            RequestOptions::TIMEOUT => 60
        ];

        $client = new \GuzzleHttp\Client();
        try {
            $excelReportRes = [
                'count'    => 0,
                'records'  => []
            ];
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
                $header = self::getHeaders();
                $timezoneList = Utils::getTimezoneList();
                Export::excel($header, $excelReportData, true, $timezoneList[$paramContainer['timezone'] - 100] . "_export");
                exit;
            }
        } catch (\Exception $e) {
            Log::info("chartReport Export Error:" . $e->getMessage());
            exit;
        }
        
        foreach ($excelReportData as $index => &$item) {
            $item['request_filled_rate_val'] = Format::rateToPer($item['request_filled_rate']);;
            $item['loads_filled_rate_val']   = Format::rateToPer($item['loads_filled_rate']);
            $item['ctr_val'] = Format::rateToPer($item['ctr']);;
        }
        unset($item);
    
        $excelReportDataMap = [];
        foreach($excelReportData as $index => $item){
            $hourFormat = sprintf("%02d", $item['hour']);
            $excelReportDataMap[$item['date_time']][$hourFormat] = $item;
        }
        
        $reportData['counts'] = $excelReportRes['count'];
        $reportData['excel_report'] = $excelReportData;
        
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
    
    //接口改版如无问题可废弃
//    private function pageRecordManual($record, $start, $limit)
//    {
//        $retRecord = [];
//
//        for ($i = $start; $i < $limit; $i++) {
//            if (!isset($record[$i])) {
//                break;
//            }
//
//            $retRecord[] = $record[$i];
//        }
//
//        return $retRecord;
//    }
    

    
    /**
     * 获取头部数组
     * @return array
     */
    private function getHeaders()
    {
        $customMetricList = array_keys($this->metricCfgList);
        $header = [
            'date_time' => __('export.date_time'),
            'hour'      => __('export.hour')
        ];
        foreach ($customMetricList as $metric) {
            $header[$metric] = __('export.'.$metric);
        }
        return $header;
    }
    
    protected static function getIndexDateRange(Request $request, $defaultDay = 6)
    {
        $reqDateRange = $request->input('daterange', '');
        if ($reqDateRange == '') {
            $startTime = time() - 86400 * $defaultDay;
            $endTime = time();
        } else {
            list($s, $e) = explode('-', $reqDateRange);
            $startTime = strtotime(trim($s));
            $endTime = strtotime(trim($e));
        }
        
        return [
            'time'   => ['start' => $startTime, 'end' => $endTime],
            'string' => ['start' => date('m/d/Y', $startTime), 'end' => date('m/d/Y', $endTime)],
        ];
    }
}
