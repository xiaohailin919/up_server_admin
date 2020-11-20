<?php
/**
 * Full Report 新
 *
 * Created by PhpStorm.
 * User: Liu
 * Date: 2020/2/18
 * Time: 1:58 PM
 */

namespace App\Http\Controllers;

use App\Helpers\ReportInputFilter;
use App\Models\MySql\AppTerm;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\MySql\Geo;
use App\Models\MySql\Publisher;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\MetricsReport;
use App\Models\MySql\MetricsSetting;

use App\Helpers\Export;
use App\Helpers\ArrayUtil;

use App\Services\CustomPage;
use App\Services\Pagination;
use App\Services\Bi;
use App\Services\UserService;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class ReportFullController extends BaseController
{

    /**
     * @var array 请求参数默认值列表
     */
    protected $defaultInput = [
        'groups'             => ['date_time'],
        'order_by'           => [],
        'compare_switch'     => 1,
        'group_compare'      => '',
        'daterange'          => '',
        'date_range_compare' => '',
        'publisher_id'       => '',
        'publisher_name'     => '',
        'publisher_type'     => 'all',
        'publisher_exclude'  => '1',
        'app_uuid'           => '',
        'app_name'           => '',
        'placement_uuid'     => '',
        'scenario_uuid'      => '',
        'unit_id'            => '',
        'geo'                => [],
        'geo_exclude'        => '1',
        'format'             => 'all',
        'nw_firm_id'         => 'all',
        'platform'           => 'all',
        'system'             => 'all',
        'segment_id'         => '',
        'sdk_version'        => '',
        'app_version'        => '',
        'timezone'           => '8',
        'channel'            => 'all',
        'is_cn_sdk'          => '-1',
        'app_type_id'        => '',
        'app_label_id'       => '',
//        'tk_sc_type'         => 'all',
        'export'             => 0,
    ];

    protected $platformLocalMap = [
        'all' => 0,
        '1'   => 1,
        '2'   => 2,
    ];

    protected $systemLocalMap = [
        'all' => 0,
        '1'   => 1,
        '2'   => 2,
    ];

    protected $formatLocalMap = [
        'all' => -1,
        '0'   =>  0,
        '1'   =>  1,
        '2'   =>  2,
        '3'   =>  3,
        '4'   =>  4,
    ];

    protected $timezoneList = [
        '-12' => 'UTC-12',
        '-11' => 'UTC-11',
        '-10' => 'UTC-10',
        '-9'  => 'UTC-9',
        '-8'  => 'UTC-8',
        '-7'  => 'UTC-7',
        '-6'  => 'UTC-6',
        '-5'  => 'UTC-5',
        '-4'  => 'UTC-4',
        '-3'  => 'UTC-3',
        '-2'  => 'UTC-2',
        '-1'  => 'UTC-1',
        '0'   => 'UTC+0',
        '1'   => 'UTC+1',
        '2'   => 'UTC+2',
        '3'   => 'UTC+3',
        '4'   => 'UTC+4',
        '5'   => 'UTC+5',
        '6'   => 'UTC+6',
        '7'   => 'UTC+7',
        '8'   => 'UTC+8',
        '9'   => 'UTC+9',
        '10'  => 'UTC+10',
        '11'  => 'UTC+11',
        '12'  => 'UTC+12',
    ];

    protected $sdkTypeMap = [
        '-1' => '-All SDK Type-',
        '1'  => '国内',
        '0'  => '国外',
    ];

    /**
     * @var array 各 GroupBY 字段对应的 Excel 列表标题显示设置
     */
    protected $tableGroupForExcel = [
        'date_time' => [
            'date_time'      => 'Date',
        ],
        'publisher_id' => [
            'publisher_id'   => 'Publisher<br/>Id',
//            'publisher_type' => 'Publisher<br/>Type',
            'publisher_name' => 'Publisher<br/>Name',
        ],
        'app_id' => array(
            'app_uuid'       => 'App<br/>UUID',
            'app_id'         => 'App ID',
            'app_name'       => 'App<br/>Name',
            'platform_name'  => 'Platform',
        ),
        'placement_id' => [
            'placement_uuid' => 'Placement<br/>UUID',
            'placement_id'   => 'Placement<br/>ID',
            'placement_name' => 'Placement<br/>Name',
        ],
        'scenario' => [
            'scenario_uuid'  => 'Scenario ID',
            'scenario_name'  => 'Scenario Name',
        ],
        'unit_id' => [
            'unit_id'        => 'AD Source<br/>ID',
            'unit_name'      => 'AD Source<br/>Name',
        ],
        'geo_short' => [
            'geo_name'       => 'Area',
        ],
        'format' => [
            'format_name'    => 'Format',
        ],
        'nw_firm_id' => [
            'nw_firm_name'   => 'Network',
        ],
        'group_id' => [
            'segment_id'     => 'Segment ID',
            'segment_name'   => 'Segment Name',
            'segment_uuid'   => 'Segment UUID',
        ],
        'sdk_version' => [
            'sdk_version'    => 'Version',
        ],
        'app_version' => [
            'app_version'    => 'APP<br/>Version',
        ],
    ];

    /**
     * @var array[] 各 GroupBY 字段对应的页面列表表头显示设置
     */
    protected $tableGroupForWeb = [
        'date_time' => [
            'name'   => 'Date',
            'fields' => ['date_time'],
        ],
        'publisher_id' => [
            'name'   => 'Publisher',
            'fields' => ['publisher_name', 'publisher_id'],
        ],
        'app_id' => [
            'name'   => 'App',
            'fields' => ['app_name', 'app_uuid', 'app_id'],
        ],
        'placement_id' => [
            'name'   => 'Placement',
            'fields' => ['placement_name', 'placement_uuid', 'placement_id'],
        ],
        'scenario' => [
            'name'   => 'Scenario',
            'fields' => ['scenario_name', 'scenario_uuid'],
        ],
        'unit_id' => [
            'name'   => 'AD Source',
            'fields' => ['unit_name', 'unit_id'],
        ],
        'geo_short' => [
            'name'   => 'Area',
            'fields' => ['geo_name'],
        ],
        'format' => [
            'name'   => 'Format',
            'fields' => ['format_name'],
        ],
        'nw_firm_id' => [
            'name'   => 'Network',
            'fields' => ['nw_firm_name'],
        ],
        'group_id' => [
            'name'   => 'Segment',
            'fields' => ['segment_name', 'segment_id', 'segment_uuid'],
        ],
        'sdk_version' => [
            'name'   => 'SDK<br/>Version',
            'fields' => ['sdk_version'],
        ],
        'app_version' => [
            'name'   => 'APP<br/>Version',
            'fields' => ['app_version'],
        ],
    ];

    // 表格 group by
    protected $tableFilter  = [];
    protected $tableFilter2 = [];

    // 表格 指标 请用 <br/> 而非 <br /> 或 <br>
    protected $tableFields = [
        'revenue'                     => 'API<br/>Revenue',
        'estimated_revenue'           => 'Estimate<br/>Revenue',
        'estimated_revenue_ecpm'      => 'Estimate<br/>eCPM',
        'dau'                         => 'DAU',
        'sdk_loads'                   => 'Load',
        'sdk_filled_loads'            => 'Filled<br/>Load',
        'fix_filled_loads'            => 'Fix<br/>Filled<br/>Load',
        'sdk_filled_loads_rate'       => 'Load<br/>Filled<br/>Rate',
        'fix_filled_load_rate'        => 'Fix Load<br/>Filled<br/>Rate',
        'sdk_request'                 => 'Request',
        'api_request'                 => 'API<br/>Request',
        'sdk_filled_request'          => 'Filled<br/>Request',
        'fix_sdk_filled_request'      => 'Fix<br/>Filled<br/>Request',
        'api_filled_request'          => 'API<br/>Filled<br/>Request',
        'sdk_filled_request_rate'     => 'Filled<br/>Request Rate',
        'fix_sdk_filled_request_rate' => 'Fix Filled<br/>Request Rate',
        'api_filled_request_rate'     => 'API Filled<br/>Request Rate',
        'sdk_show'                    => 'Show',
        'sdk_impression'              => 'Impression',
        'fix_sdk_impression'          => 'Fix<br/>Impression',
        'api_impression'              => 'API<br/>Impression',
        'sdk_ecpm'                    => 'eCPM',
        'fix_sdk_ecpm'                => 'Fix<br/>eCPM',
        'api_ecpm'                    => 'API<br/>eCPM',
        'ready_request'               => 'isReady',
        'ready_rate'                  => 'isReady<br/>Rate',
        'show_failed'                 => 'Show<br/>Fail',
        'show_success_rate'           => 'Show<br/>Success<br/>Rate',
        'tk_rv_start'                 => 'Video Start',
        'unit_rv_start'               => 'API<br/>Video Start',
        'tk_rv_complete'              => 'Video Complete',
        'unit_rv_complete'            => 'API<br/>Video Complete',
        'sdk_click'                   => 'Click',
        'api_click'                   => 'API<br/>Click',
        'sdk_ctr'                     => 'CTR',
        'fix_sdk_ctr'                 => 'Fix<br/>CTR',
        'api_ctr'                     => 'API<br/>CTR',
        'cost'                        => 'Cost<br/>',
        'profit'                      => 'Profit<br/>',
    ];

    // 表格 可排序字段
    protected $tableSort = [
        'date_time',
        'dau',
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

    protected $tableMoney = [
        'revenue', 'sdk_ecpm', 'fix_sdk_ecpm', 'api_ecpm', 'cost', 'profit', 'estimated_revenue_ecpm', 'estimated_revenue'
    ];

    protected $tableRate = [
        'sdk_filled_loads_rate', 'sdk_filled_request_rate', 'api_filled_request_rate', 'sdk_ctr', 'api_ctr',
        'fix_filled_load_rate', 'fix_sdk_filled_request_rate', 'fix_sdk_ctr', 'ready_rate', 'show_success_rate'
    ];

    protected $allowOrderByRevenue = [
        'date_time',
        'publisher_id',
        'app_id',
        'placement_id',
        'unit_id',
        'geo_short',
        'format',
        'nw_firm_id',
    ];

    protected $allowOrderByDau = [
        'date_time',
        'app_id',
        'geo_short',
        'publisher_id',
    ];

    public function index(Request $request){
        $this->checkAccessPermission('full_report_list');

        $input = $request->input();
        foreach ($this->defaultInput as $param => $default) {
            $input[$param] = !isset($request[$param]) || $request[$param] == null ? $default : $request[$param];
        }

        $publisherIdList = ReportInputFilter::getPublisherIdsByFilters(
            ArrayUtil::explodeString($input['publisher_id'], ','),
            $input['publisher_name'], $input['publisher_type'], $input['channel'], $input['publisher_exclude'] == '2'
        );

        $appIdListByType  = ReportInputFilter::getAppIdsByTermIds($input['app_type_id'] == '' ? [] : [$input['app_type_id']], false);
        $appIdListByLabel = ReportInputFilter::getAppIdsByTermIds($input['app_label_id'] == '' ? [] : [$input['app_label_id']], false);

        if ($appIdListByLabel === false || $appIdListByType === false) {
            $appIdListByTerm = false;
        } else if ($appIdListByLabel != [] && $appIdListByType != []) {
            $appIdListByTerm = array_intersect($appIdListByLabel, $appIdListByType);
            $appIdListByTerm = $appIdListByTerm == [] ? false : $appIdListByTerm;
        } else {
            $appIdListByTerm = array_merge($appIdListByLabel, $appIdListByType);
        }
        $appIdList = ReportInputFilter::getAppIdsByUuidsAndName(ArrayUtil::explodeString(trim($input['app_uuid']), ','), $input['app_name']);
        if ($appIdList === false || $appIdListByTerm === false) {
            $appIdList = false;
        } else if ($appIdList != [] && $appIdListByTerm != []) {
            $appIdList = array_intersect($appIdList, $appIdListByTerm);
        } else {
            $appIdList = array_merge($appIdList, $appIdListByTerm);
        }
//        $appIdList       = ReportInputFilter::getAppIdsByUuidsAndName(ArrayUtil::explodeString(trim($input['app_uuid']), ','), $input['app_name']);
        $placementIdList  = ReportInputFilter::getPlacementIdsByUuids(ArrayUtil::explodeString(trim($input['placement_uuid']), ','));
        $scenarioIdList   = ReportInputFilter::getScenarioIdsByUuids(ArrayUtil::explodeString(trim($input['scenario_uuid']), ','));
        $segmentIdList    = ReportInputFilter::getSegmentIdsByUuids(ArrayUtil::explodeString(trim($input['segment_id']), ','));
        $geoIdList        = ReportInputFilter::getGeoIdsByIds($input['geo'], $input['geo_exclude'] == '2');
        $dateRange        = $input['daterange'];
        $dateRangeCompare = $input['date_range_compare'];
        $unitIdList       = ArrayUtil::explodeString(trim($input['unit_id']), ',', 'int');
        $format           = $input['format'];
        $nwFirmId         = $input['nw_firm_id'];
        $platform         = $input['platform'];
        $system           = $input['system'];
        $segmentId        = ArrayUtil::explodeString(trim($input['segment_id']), ',', 'int');
        $sdkVersionList   = ArrayUtil::explodeString(trim($input['sdk_version']), ',');
        $appVersionList   = ArrayUtil::explodeString(trim($input['app_version']), ',');
        $timezone         = $input['timezone'];
        $isCnSDK          = $input['is_cn_sdk'];
        $export           = $input['export'];
        $groupBy          = $input['groups'];
        $orderBy          = $input['order_by'];
        $compareSwitch    = $input['compare_switch'];
        $groupByCompare   = $input['group_compare'];

        /* 过滤开发者 */
        if (in_array('publisher_id', $groupBy, true)
            || in_array('app_id', $groupBy, true)
            || in_array('placement_id', $groupBy, true)
            || $request->has('publisher_id')
            || $request->has('publisher_name')
            || $request->has('app_uuid')
            || $request->has('app_name')
            || $request->has('placement_uuid')) {
            /* 用户可视的开发者列表 */
            $publicPublisherIds = UserService::getPublisherIdListByUserId(auth()->id());

            /* 如果开发者列表无搜索，使用允许列表中的开发者，如果有搜索，取并集 */
            if (empty($publisherIdList)) {
                $publisherIdList = $publicPublisherIds;
            } else {
                $publisherIdList = array_values(array_intersect($publicPublisherIds, $publisherIdList));
            }
            /* 取交集之后为空，表示搜索为空 */
            if (empty($publisherIdList)) {
                $publisherIdList = false;
            }
        }

        $paramChecked = $publisherIdList !== false && $appIdList !== false && $placementIdList !== false &&
            $scenarioIdList !== false && $segmentIdList !== false && $geoIdList !== false;

        // 解析日期范围，如果日期比较开关开启，则解析比较的日期范围，注意：如果比较的日期范围大于搜索日期范围，则截取比较日期范围最后几天（和搜索日期范围一样）
        $parseDateRange = $this->parseDateRange($dateRange);
        if((int)$compareSwitch === 2){
            $parseDateRangeCompare = $this->parseDateRange($dateRangeCompare);
            $groupBy = [$groupByCompare];
        }else{
            $parseDateRangeCompare['string'] = ['start' => '', 'end'   => ''];
        }
        // 如果 group by placement 需要把 app 也 group by
        if(in_array('placement_id', $groupBy, true) && !in_array('app_id', $groupBy, true)){
            $groupBy[] = 'app_id';
        }

        // order by
        if(empty($orderBy) || !$request->has('order_by')){
            $orderBy      = array('date_time' => 'desc');
            $orderByParam = array(
                ['date_time'      => 'desc'],
                ['api_revenue'    => 'desc'],
                ['sdk_loads'      => 'desc'],
                ['sdk_impression' => 'desc']
            );
            if (!in_array('date_time', $groupBy, true)) {
                unset($orderByParam[0], $orderBy['date_time']);
                $orderBy['api_revenue'] = 'desc';
            }
        }else{
            $orderByParam = array();
            foreach ($orderBy as $key => $value) {
                if($key === 'revenue'){
                    $key = 'api_revenue';
                }
                $orderByParam[] = [$key => $value];
            }
        }

        // 不能 order by revenue
        if(count(array_intersect($groupBy, $this->allowOrderByRevenue)) <= 0){
            foreach ($orderByParam as $key => $value) {
                if(array_keys($value)[0] === 'api_revenue'){
                    unset($orderByParam[$key]);
                }
            }
            $orderByParam = array_values($orderByParam);
        }

        // 不能 order by dau
        if(count(array_intersect($groupBy, $this->allowOrderByDau)) <= 0){
            foreach ($orderByParam as $key => $value) {
                if(array_keys($value)[0] === 'dau'){
                    unset($orderByParam[$key]);
                }
            }
            $orderByParam = array_values($orderByParam);
        }

        $limit = 25;
        $page  = CustomPage::getPage($limit, 'page');

        // 导出
        if ((int)$export === 1) {
            $page  = 1;
            $limit = 10000;
        }

        // 1 中国 （只选中国）
        // 2 海外 （反选中国，除了中国之外的所有地区）
        // 3 其他 （选中了其他地区，可能包含中国）
        // 0 全球 （没选）
        $region = 0;
        if(!empty($geoIdList)){
            if(count($input['geo']) == 1 && $input['geo'][0] == 'CN' && $input['geo_exclude'] == '2'){
                $region = 2;
            }else if(count($geoIdList) == 1 && $geoIdList[0] == 'CN'){
                $region = 1;
            }else if(count($geoIdList) > 1){
                $region = 3;
            }
        }

        $biService = new Bi();

        // BI param
        $param = [
            'start_time'        => (int)date('Ymd', $parseDateRange['time']['start']),
            'end_time'          => (int)date('Ymd', $parseDateRange['time']['end']),
            'publisher_id_list' => $publisherIdList,
            'app_id_list'       => $appIdList,
            'placement_id_list' => $placementIdList,
            'scenario_list'     => $scenarioIdList,
            'ad_source_id_list' => $unitIdList,
            'segment_id_list'   => $segmentIdList,
            'geo_short'         => $geoIdList,
            'region'            => $region,
            'format'            => $this->formatLocalMap[$format],
            'nw_firm_id'        => (int)$nwFirmId,
            'platform'          => $this->platformLocalMap[$platform],
            'system'            => $this->systemLocalMap[$system],
            'sdk_version_list'  => $sdkVersionList,
            'app_version_list'  => $appVersionList,
            'is_cn_sdk'         => (int)$isCnSDK,
            'timezone'          => (int)$timezone + 100,
            'group_by'          => $groupBy,
            'order_by'          => array_values($orderByParam),
            'offset'            => ($page - 1) * $limit,
            'limit'             => $limit,
        ];

//        return $param;

        $data = $paramChecked ? $this->getBiData($param) : ['list' => [], 'total' => 0];
//        return $this->getBiData($param);
        $data['list']                = $biService->fillFilterInfo($data['list'], $groupBy);
//        return $data;

        $data['list_compare']        = [];
        $data['list_compare_result'] = [];

        // 对比数据
        $dateStart = $param['start_time'];
        $dateEnd   = $param['end_time'];
        $compareDateStart = $compareDateEnd = 0;
        if((int)$compareSwitch === 2) {
            $compareDateStart = (int)date('Ymd', $parseDateRangeCompare['time']['start']);
            $compareDateEnd   = (int)date('Ymd', $parseDateRangeCompare['time']['end']);

            $param['offset']     = 0;
            $param['start_time'] = $compareDateStart;
            $param['end_time']   = $compareDateEnd;

            switch($groupBy[0]){
                case 'publisher_id':
                    foreach($data['list'] as $val){
                        if(in_array($val['publisher_id'], $param['publisher_id_list'], false)){
                            continue;
                        }
                        $param['publisher_id_list'][] = $val['publisher_id'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'publisher_id');
                    break;
                case 'app_id':
                    foreach($data['list'] as $val){
                        if(in_array($val['app_id'], $param['app_id_list'], false)){
                            continue;
                        }
                        $param['app_id_list'][] = $val['app_id'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'app_id');
                    break;
                case 'placement_id':
                    foreach($data['list'] as $val){
                        if(in_array($val['placement_id'], $param['placement_id_list'], false)){
                            continue;
                        }
                        $param['placement_id_list'][] = $val['placement_id'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'placement_id');
                    break;
                case 'scenario':
                    foreach($data['list'] as $val){
                        if(in_array($val['scenario'], $param['scenario_list'], false)){
                            continue;
                        }
                        $param['scenario_list'][] = $val['scenario'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'scenario');
                    break;
                case 'format':
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'format');
                    break;
                case 'group_id': // segment
                    foreach($data['list'] as $val){
                        if(in_array($val['group_id'], $param['segment_id_list'],false)){
                            continue;
                        }
                        $param['segment_id_list'][] = $val['group_id'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'group_id');
                    break;
                case 'geo_short':
                    foreach($data['list'] as $val){
                        if(in_array($val['geo_short'], $param['geo_short'],false)){
                            continue;
                        }
                        $param['geo_short'][] = $val['geo_short'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'geo_short');
                    break;
                case 'nw_firm_id':
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'nw_firm_id');
                    break;
                case 'unit_id':
                    foreach($data['list'] as $val){
                        if(in_array($val['unit_id'], $param['ad_source_id_list'],false)){
                            continue;
                        }
                        $param['ad_source_id_list'][] = $val['unit_id'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'unit_id');
                    break;
                case 'sdk_version':
                    foreach($data['list'] as $val){
                        if(in_array('UA_' . $val['sdk_version'], $param['sdk_version_list'], false)){
                            continue;
                        }
                        $param['sdk_version_list'][] = 'UA_' . $val['sdk_version'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'sdk_version');
                    break;
                case 'app_version':
                    foreach($data['list'] as $val){
                        if(in_array($val['app_version'], $param['app_version_list'], false)){
                            continue;
                        }
                        $param['app_version_list'][] = $val['app_version'];
                    }
                    $dataCompare = $this->getBiData($param);
                    $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
                    $data = $biService->fillFullReportCompare($data, $dataCompare, 'app_version');
                    break;
                default:
                    // date_time
                    try {
                        $dateStart1 = new DateTime($dateStart);
//                        $dateStart2 = new DateTime($compareDateStart);
                        $dateEnd1   = new DateTime($dateEnd);
//                        $dateEnd2   = new DateTime($compareDateEnd);
                        $extra = [];
                        $dateDiff = date_diff($dateStart1, $dateEnd1)->d;
                        for($i = 0; $i <= $dateDiff; $i++){
                            $dateTmp1 = date('Ymd', strtotime("-{$i} day", strtotime($dateEnd)));
                            $dateTmp2 = date('Ymd', strtotime("-{$i} day", strtotime($compareDateEnd)));
                            $extra[(string)$dateTmp1] = (string)$dateTmp2;
                        }
//                    return $extra;
                        $dataCompare = $this->getBiData($param);
                        $dataCompare['list'] = $biService->fillFilterInfo($dataCompare['list'], $groupBy);
//                    return $dataCompare;
                        $data = $biService->fillFullReportCompare($data, $dataCompare, 'date_time', $extra);
                    } catch (Exception $e) {
                        Log::error($e->getTraceAsString());
                    }
                    break;
            }
        }

//        return $data;

        // 表头
        $metricsSettingModel  = new MetricsSetting();
        $metricsSettingFields = $metricsSettingModel->getFullReportSettingFields();
        // table filter
        foreach($groupBy as $val){
            $this->tableFilter += $this->tableGroupForExcel[$val];
            $this->tableFilter2[] = $this->tableGroupForWeb[$val];
//            $this->tableFilter2 = array_merge($this->tableFilter2, $this->tableGroup2[$val]);
        }
        if((int)$compareSwitch === 2 && $groupBy[0] !== 'date_time'){
            $this->tableFilter    = array_merge($this->tableFilter, $this->tableGroupForExcel['date_time']);
            $this->tableFilter2[] = $this->tableGroupForWeb['date_time'];
        }
        // table field
        foreach($this->tableFields as $key => $val){
            if(!array_key_exists($key, $metricsSettingFields)){
                unset($this->tableFields[$key]);
            }
        }

        $this->tableFields = array_merge($this->tableFilter, $this->tableFields);

        $this->tableFields = $this->tableFieldsFilter($this->tableFields, $groupBy, $param);

//        return $this->tableFields;

        // 导出报表数据
        if ((int)$export === 1) {
            $header = [];
            foreach ($this->tableFields as $key => $val) {
                $header[$key] = str_replace('<br/>', ' ', $val);
            }

            $exportData = [];
            foreach ($data['list'] as $k => $v) {
                $row = $v;
                if(!in_array('date_time', $groupBy, false)){
                    $row['date_time'] = "{$dateStart} ~ {$dateEnd}";
                }
                foreach ($row as $kk => $vv) {
                    if (empty($vv)) {
                        $row[$kk] = '-';
                    }
                }
                $exportData[] = $row;

                if((int)$compareSwitch === 2){
                    $exportData[] = $biService->fillFullReportRow($data['list_compare'][$k], true, "{$compareDateStart} ~ {$compareDateEnd}");
                    $exportData[] = $biService->fillFullReportRow($data['list_compare_result'][$k],false);
                }
            }

//            return $exportData;

            Export::excel($header, $exportData, true, $this->timezoneList[$timezone] . '_export');
            exit;
        }

        $total  = data_get($data, 'total', 0);
        $report = Pagination::paginate($total, $data['list'], $limit, 'page', $page);

        $metricsSetting = $metricsSettingModel->getFullReportSettings();
        $metricsSettingIds = array_column($metricsSetting, 'metrics_id');

//        /* 处理 AllMetrics, 给 allMetrics 增加 id 信息 */
//        $fullReportFields = (new MetricsReport())->getFullReportFields();
//        foreach ($fullReportFields as $field) {
//            $allMetrics[$field['field']][] = $field['id'];
//        }

        //        return $this->tableFilter2;
        $input['groups'] = $groupBy;
        $defaultOrderBy = ['date_time' => 'desc'];
        $input['order_by'] = $orderBy == $defaultOrderBy ? '' : $orderBy;

        return view('report-full.index')
            ->with('report', $report)
            ->with('pageAppends', $input)
            ->with('dateRange', $parseDateRange['string'])
            ->with('dateRangeCompare', $parseDateRangeCompare['string'])
            ->with('publisherTypeMap', Publisher::getPublisherTypeMap())
            ->with('geoMap', Geo::getGeoMap())
            ->with('reportCompare', $data['list_compare'])
            ->with('reportCompareResult', $data['list_compare_result'])
            ->with('formatMap', Placement::getFormatMap())
            ->with('nwFirmMap', NetworkFirm::getAllIntegratedNwFirmMap())
            ->with('customNwIdNameWithPublisherMap', NetworkFirm::getCustomNwIdNameWithPublisherMap())
            ->with('platformMap', App::getPlatformMap())
            ->with('sdkTypeMap', $this->sdkTypeMap)
            ->with('tableFilter', $this->tableFilter)
            ->with('tableFilter2', $this->tableFilter2)
            ->with('tableFields', $this->tableFields)
            ->with('sortFields', $this->tableSort)
            ->with('tableMoney', $this->tableMoney)
            ->with('tableRate', $this->tableRate)
            ->with('timezoneList', $this->timezoneList)
            ->with('channelMap', Publisher::getChannelMap())
            ->with('appTypeMap', AppTerm::getAppTypeMap())
            ->with('appLabelMap', AppTerm::getLabelParentChildrenMap())
            ->with('metricsFields', MetricsReport::getFullReportFields())
            ->with('metricsSettingIds', $metricsSettingIds);
    }

    /**
     * 从BI获取报表数据
     * @param  array $param
     * @return array
     */
    private function getBiData($param): array
    {
        $biApi = env('BI_SERVICE_ADMIN_REPORT_V2');
        Log::info('ReportFullController getBiData: ' . ' API: ' . $biApi . '; param: ', $param);

        $data = [
            'list'  => [],
            'total' => 0,
        ];
        $client = new Client();
        try {
            $response = $client->post($biApi, [RequestOptions::JSON => $param]);
            if ((int)$response->getStatusCode() === 200) {
                $tmpData = json_decode($response->getBody(), true);
                if ((int)$tmpData['code'] === 0 && $tmpData['data'] && $tmpData['data']['count']) {
                    $data = [
                        'list' => $tmpData['data']['list'],
                        'total' => $tmpData['data']['count'],
                    ];
                }
            }
        } catch (RequestException $e) {
            Log::info('ReportFullController getBiData error: ' . json_encode($e->getMessage()));
        }

        return $data;
    }

    /**
     * 过滤不需要展示的表字段
     * @param array $tableFields
     * @param array $groupBy
     * @param array $param
     * @return array
     */
    private function tableFieldsFilter($tableFields, $groupBy = [], $param = []): array
    {
        unset($tableFields['tk_rv_start'], $tableFields['unit_rv_start'], $tableFields['tk_rv_complete'], $tableFields['unit_rv_complete']);
        if($param['format'] > -1 || in_array('format', $groupBy, false)){
            unset($tableFields['dau']);
        }
        if(!empty($param['nw_firm_id'])
            || !empty($param['ad_source_id_list'])
            || in_array('unit_id', $groupBy, false)
            || in_array('nw_firm_id', $groupBy, false)) {
            unset(
                $tableFields['dau'],
                $tableFields['sdk_loads'],
                $tableFields['sdk_filled_loads_rate'],
                $tableFields['cost'],
                $tableFields['profit'],
                $tableFields['fix_filled_loads'],
                $tableFields['fix_filled_load_rate'],
                $tableFields['sdk_filled_loads'],
                $tableFields['ready_request'],
                $tableFields['ready_rate'],
                $tableFields['show_failed'],
                $tableFields['show_success_rate']
            );
        }
        if(!empty($param['segment_id_list'])
            || !empty($param['sdk_version_list'])
            || !empty($param['app_version_list'])
            || in_array('group_id', $groupBy, false)
            || in_array('sdk_version', $groupBy, false)
            || in_array('app_version', $groupBy, false)) {
            unset(
                $tableFields['revenue'],
                $tableFields['dau'],
                $tableFields['api_request'],
                $tableFields['api_filled_request'],
                $tableFields['api_filled_request_rate'],
                $tableFields['api_impression'],
                $tableFields['api_ecpm'],
                $tableFields['unit_rv_start'],
                $tableFields['unit_rv_complete'],
                $tableFields['api_click'],
                $tableFields['api_ctr'],
                $tableFields['cost'],
                $tableFields['profit']
            );
        }
        if(!empty($param['scenario_list']) || in_array('scenario', $groupBy, false)){
            unset(
                $tableFields['revenue'],
                $tableFields['dau'],
                $tableFields['api_request'],
                $tableFields['api_filled_request'],
                $tableFields['api_filled_request_rate'],
                $tableFields['api_impression'],
                $tableFields['api_ecpm'],
                $tableFields['unit_rv_start'],
                $tableFields['unit_rv_complete'],
                $tableFields['api_click'],
                $tableFields['api_ctr'],
                $tableFields['cost'],
                $tableFields['profit'],
                $tableFields['ready_request'],
                $tableFields['ready_rate'],
                $tableFields['show_failed'],
                $tableFields['show_success_rate']
            );
        }
        // 搜索placement，但是没有group by app/placement
        if(!empty($param['placement_id_list'])
            && !in_array('app_id', $groupBy)
            && !in_array('placement_id', $groupBy)){
            unset($tableFields['dau']);
        }
        // 不group by date，筛选项天数多于1天，不显示dau。只有选1天才有dau
        if(($param['end_time'] - $param['start_time'] >= 1) && !in_array('date_time', $groupBy, false)){
            unset($tableFields['dau']);
        }

        return $tableFields;
    }

    /**
     * 解析date range
     * @param string $dateRange
     * @param int    $defaultDay
     * @return array
     */
    private function parseDateRange($dateRange, $defaultDay = 6): array
    {
        $reqDateRange = $dateRange;
        if ($reqDateRange === '') {
            $startTime = time() - 86400 * $defaultDay;
            $endTime = time();

        } else {
            list($s, $e) = explode('-', $reqDateRange);
            $startTime = strtotime(trim($s));
            $endTime = strtotime(trim($e));
        }

        return [
            'time' => [
                'start' => $startTime,
                'end'   => $endTime
            ],
            'string' => [
                'start' => date('m/d/Y', $startTime),
                'end'   => date('m/d/Y', $endTime)
            ],
        ];
    }
}