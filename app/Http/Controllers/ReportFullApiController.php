<?php

namespace App\Http\Controllers;

use App\Helpers\ArrayUtil;
use App\Helpers\ReportInputFilter;
use App\Models\MySql\App;
use App\Models\MySql\Base;
use App\Models\MySql\Geo;
use App\Models\MySql\Placement;
use App\Models\MySql\Scenario;
use App\Models\MySql\Segment;
use App\Models\MySql\Unit;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportFullApiController extends ApiController
{
    /**
     * 左边为 BI Group 的参数，右边为本接口接收的参数，目的是为了统一字段名和过滤非必要字段，若 BI 参数传入不支持字段，BI 接口会忽略该字段
     *
     * @var string[]
     */
    private $groupByFieldsMap = [
        'date_time'    => 'date_time',
        'format'       => 'format',       // 对应字段 format
        'nw_firm_id'   => 'nw_firm_id',   // 对应字段 nw_firm_id
        'publisher_id' => 'publisher_id', // 对应字段 publisher_id_list
        'app_id'       => 'app_id',       // 对应字段 app_id_list
        'placement_id' => 'placement_id', // 对应字段 placement_id_list
        'unit_id'      => 'unit_id',      // 对应字段 unit_id_list
        'scenario_id'  => 'scenario',     // 对应字段 scenario_id_list
        'segment_id'   => 'group_id',     // 对应字段 segment_id_list
        'geo_short'    => 'geo_short',    // 对应字段 geo_short_list
        'sdk_version'  => 'sdk_version',  // 对应字段 sdk_version_list
        'app_version'  => 'app_version',  // 对应字段 app_version_list
    ];

    private $orderByFields = [
        'date_time', 'sdk_request', 'api_request', 'sdk_filled_request', 'api_filled_request',
        'sdk_impression', 'api_impression', 'sdk_loads', 'sdk_click', 'api_click', 'api_revenue',
        'cost', 'sdk_show', 'dau'
    ];

    private $allowOrderByRevenue = [
        'date_time',
        'publisher_id',
        'app_id',
        'placement_id',
        'unit_id',
        'geo_short',
        'format',
        'nw_firm_id',
    ];

    private $allowOrderByDau = [
        'date_time',
        'app_id',
        'geo_short',
        'publisher_id',
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $biParam = $this->generateBIParams($request);
//        return $this->jsonResponse($biParam);
        $biRes = $this->getBIData($biParam);

        $data = $biRes['data'];


        return $this->jsonResponse($biRes['data'], $biRes['code'], $biRes['msg']);
    }

    /**
     * 生成 BI 参数
     *
     * doc：https://confluence.magicgame001.com/pages/viewpage.action?pageId=18749577
     * BI 参数
     * 必传：offset、limit、start_time、end_time、timezone、format
     *
     * {
     *     "start_time"        : 20200827,
     *     "end_time"          : 20200827,
     *     "publisher_id_list" : [],
     *     "app_id_list"       : [],
     *     "placement_id_list" : [],
     *     "scenario_list"     : [],
     *     "ad_source_id_list" : [],
     *     "segment_id_list"   : [],
     *     "geo_short"         : [],
     *     "format"            : -1,
     *     "nw_firm_id"        : 0,
     *     "platform"          : 0,
     *     "system"            : 0,
     *     "sdk_version_list"  : [],
     *     "app_version_list"  : [],
     *     "is_cn_sdk"         : 1,
     *     "sc_type"           : 1,
     *     "timezone"          : 108,
     *     "group_by"          : ["app_id"],
     *     "order_by"          : [["app_id": "asc"]],
     *     "offset"            : 0,
     *     "limit"             : 25
     *
     * @param Request $request
     * @return array
     */
    private function generateBIParams(Request $request): array
    {

        $limit = $request->query('page_size', 25);
        $offset = ($request->query('page_no', 1) - 1) * $limit;

        $biParams = [
            'limit'      => (int)$limit,
            'offset'     => (int)$offset,
            'start_time' => (int)$request->query('start_time', date('Ymd')), // 开始日期
            'end_time'   => (int)$request->query('start_time', date('Ymd')), // 结束日期
            'timezone'   => (int)$request->query('timezone', 108),           // 时区必传
            'format'     => (int)$request->query('format', -1),              // 广告样式必传，不传 BI 默认值为 0，表示 native
            'is_cn_sdk'  => (int)$request->query('is_cn_sdk', -1),           // SDK 地区必传，不传 BI 默认值为 0，表示非中国
        ];

        /* Group By，已验证 */
        $biParams['group_by'] = [];
        if (is_array($request->query('group_by_field_list'))) {
            $tmpList = $request->query('group_by_field_list');
            foreach ($tmpList as $item) {
                if (array_key_exists($item, $this->groupByFieldsMap)) {
                    $biParams['group_by'][] = $this->groupByFieldsMap[$item];
                }
            }
        }
        if (empty($biParams['group_by'])) {
            $biParams['group_by'] = ['date_time'];
        }

        /* Order By，已验证 */
        $biParams['order_by'] = [];
        $defaultOrderBy = [
            ['date_time'      => 'desc'],
            ['api_revenue'    => 'desc'],
            ['sdk_loads'      => 'desc'],
            ['sdk_impression' => 'desc'],
        ];
        if (is_array($request->query('order_by_field_list'))) {
            $tmpList = $request->query('order_by_field_list');
            $directionList = $request->query('order_by_direction_list');
            foreach ($tmpList as $idx => $item) {
                if (in_array($item, $this->orderByFields, true)) {
                    $biParams['order_by'][] = [$item => $directionList[$idx] ?? 'desc'];
                }
            }
        }
        /* 若 Group By 中没有包含可 Order By revenue 的字段，则去掉该参数 */
        if (count(array_intersect($biParams['group_by'], $this->allowOrderByRevenue)) < 1) {
            foreach ($biParams['order_by'] as $idx => $value) {
                if (array_key_exists('api_revenue', $biParams['order_by'][$idx])) {
                    unset($biParams['order_by'][$idx]);
                }
            }
            foreach ($defaultOrderBy as $idx => $value) {
                if (array_key_exists('api_revenue', $defaultOrderBy[$idx])) {
                    unset($defaultOrderBy[$idx]);
                }
            }
        }
        /* 若 Group By 中没有包含可 Order By dau 的字段，则去掉该参数 */
        if (count(array_intersect($biParams['group_by'], $this->allowOrderByDau)) < 1) {
            foreach ($biParams['order_by'] as $idx => $value) {
                if (array_key_exists('dau', $biParams['order_by'][$idx])) {
                    unset($biParams['order_by'][$idx]);
                }
            }
        }
        if (empty($biParams['order_by'])) {
            $biParams['order_by'] = $defaultOrderBy;
            /* 没有 Group By 日期，则不允许将日期排序 */
            if (!in_array('date_time', $biParams['group_by'], true)) {
                unset($biParams['order_by'][0]);
            }
        }
        $biParams['order_by'] = array_values($biParams['order_by']);


        /* 广告厂商 ID，测试通过 */
        if ($request->query('nw_firm_id', -1) > 0) {
            $biParams['nw_firm_id'] = (int)$request->query('nw_firm_id');
        }
        /* 系统平台，测试通过 */
        if (in_array($request->query('platform', -1), [1, 2], false)) {
            $biParams['platform'] = (int)$request->query('platform');
        }
        /* 开发者 ID 列表，依据 ID 列表、开发者类型、开发者渠道、包含不包含，测试通过 */
        if (is_array($request->query('publisher_id_list'))) {
            $tmpList   = $request->query('publisher_id_list');
            $pubType   = $request->query('publisher_type', -1); // 开发者类型：黑盒、白盒
            $channel   = $request->query('publisher_channel', -1); // 开发者渠道：TopOn、233、Dlads
            $isExclude = $request->query('publisher_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE; // 包含、不包含
            $biParams['publisher_id_list'] =
                ReportInputFilter::getPublisherIdsByFilters($tmpList, '', $pubType, $channel, $isExclude);
        }
        /* APP ID 列表，依据 ID 列表、包含不包含，测试通过 */
        if (is_array($request->query('app_id_list'))) {
            $tmpList = $request->query('app_id_list');
            $isExclude = $request->query('app_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['app_id_list'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, App::TABLE, 'id', false)
                : ArrayUtil::intElements($tmpList);
        }
        /* APP ID 列表，依据应用类型、应用标签、包含不包含，注意二者是相互独立的联系，但放在同一张表中，一次性查询出来，测试通过 */
        if (is_array($request->query('app_type_id_list')) || is_array($request->query('app_label_id_list'))) {
            $typeIdList   = $request->get('app_type_id_list',  []);
            $labelIdList  = $request->get('app_label_id_list', []);
            $typeExclude  = empty($typeIdList)  ? true : $request->get('app_type_id_list_exclude',  Base::INCLUDE) == Base::EXCLUDE;
            $labelExclude = empty($labelIdList) ? true : $request->get('app_label_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $appIdListByTerms = ReportInputFilter::getAppIdListByAppTerm($typeIdList, $typeExclude, $labelIdList, $labelExclude);
            if ($appIdListByTerms === false) {
                $biParams['app_id_list'] = false;
            } else {
                $biParams['app_id_list'] = isset($biParams['app_id_list'])
                    ? array_values(array_intersect($appIdListByTerms, $biParams['app_id_list']))
                    : $appIdListByTerms;
            }
        }
        /* Placement ID 列表，测试通过 */
        if (is_array($request->query('placement_id_list'))) {
            $tmpList = $request->query('placement_id_list');
            $isExclude = $request->query('placement_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['placement_id_list'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, Placement::TABLE, 'id', false)
                : ArrayUtil::intElements($tmpList);
        }
        /* Scenario ID 列表，测试通过 */
        if (is_array($request->query('scenario_id_list'))) {
            $tmpList = $request->query('scenario_id_list');
            $isExclude = $request->query('scenario_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['scenario_list'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, Scenario::TABLE, 'id', false)
                : ArrayUtil::intElements($tmpList);
        }
        /* Segment ID 列表，测试通过 */
        if (is_array($request->query('segment_id_list'))) {
            $tmpList = $request->query('segment_id_list');
            $isExclude = $request->query('segment_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['segment_id_list'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, Segment::TABLE, 'id', false)
                : ArrayUtil::intElements($tmpList);
        }
        /* 广告源 ID 列表，测试通过 */
        if (is_array($request->query('unit_id_list'))) {
            $tmpList = $request->query('unit_id_list');
            $isExclude = $request->query('unit_id_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['ad_source_id_list'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, Unit::TABLE, 'id', false)
                : ArrayUtil::intElements($tmpList);
        }
        /* 地区短码列表，测试通过 */
        if (is_array($request->query('geo_short_list'))) {
            $tmpList = $request->query('geo_short_list');
            $isExclude = $request->query('geo_short_list_exclude', Base::INCLUDE) == Base::EXCLUDE;
            $biParams['geo_short'] = $isExclude
                ? ReportInputFilter::getExcludeValues($tmpList, Geo::TABLE, 'short', false) : $tmpList;
        }
        /* SDK 版本列表 */
        if (is_array($request->query('sdk_version_list'))) {
            $biParams['sdk_version_list'] = $request->query('sdk_version_list');
        }
        /* APP 版本列表 */
        if (is_array($request->query('app_version_list'))) {
            $biParams['app_version_list'] = $request->query('app_version_list');
        }

        return $biParams;
    }

    /**
     * 获取 BI 数据
     *
     * @param $param
     * @return array|mixed
     */
    private function getBIData($param)
    {
        $biApi = env('BI_SERVICE_ADMIN_REPORT_V2');

        $client = new Client();
        $response = $client->post($biApi, [RequestOptions::JSON => $param]);

        if ($response->getStatusCode() != 200) {
            throw new HttpResponseException($this->jsonResponse(json_decode($response->getBody(), true), 9999, 'BI Api Error', $response->getStatusCode()));
        }

        return json_decode($response->getBody(), true);
    }
}
