<?php


namespace App\Services;


use App\Helpers\ReportInputFilter;
use App\Models\MySql\Base;
use Illuminate\Http\Request;

class ReportFullService
{

    /**
     * 将传入的参数转化为 BI 请求参数
     *
     * @param Request $request
     * @return array
     */
    public static function parseBIParams(Request $request): array
    {
        /* 数组传空值 BI 接口默认跳过该参数 */
        $biParam = [
            'offset'     => 0,
            'limit'      => 25,
            'start_time' => date('Ymd'),
            'end_time'   => date('Ymd'),
            'time_zone'  => 108,
            'format'     => -1,             // -1 表示全部
            'is_cn_sdk'  => -1,             // -1 表示全部
        ];

        /* 必填字段 */
        $biParam['start_time'] = (int)$request->query('start_date', $biParam['start_time']);
        $biParam['end_time']   = (int)$request->query('end_date', $biParam['end_time']);
        $biParam['time_zone']  = (int)$request->query('time_zone', 8) + 100;
        $biParam['format']     = (int)$request->query('format', $biParam['format']);
        $biParam['is_cn_sdk']  = (int)$request->query('sdk_type', $biParam['is_cn_sdk']);
        $biParam['limit']      = (int)$request->query('limit', $biParam['limit']);
        $biParam['offset']     = (int)($request->query('page_no', 1) - 1) * $biParam['limit'];

        /* 指定默认值字段 */
        $biParam['group_by'] = $request->query('group_by', ['date_time']);
        if ($request->has('order_by_field_list')
            && count($request->query('order_by_field_list')) == count($request->query('order_by_direction_list'))) {
            $orderByFieldList = $request->query('order_by_field_list');
            $orderByDirectionList = $request->query('order_by_direction_list');
            for ($i = 0, $iMax = count($orderByFieldList); $i < $iMax; $i++) {
                if (!in_array($orderByDirectionList[$i], [1, 2], false)) {
                    continue;
                }
                $biParam['order_by'][] = [$orderByFieldList[$i], $orderByDirectionList[$i] == 1 ? "DESC" : "ASC"];
            }
        } else {
            $biParam['order_by'] = ['date_time', 'DESC'];
        }

        /* 非必填非数组字段 */
        if ($request->has('nw_firm_id')) {
            $biParam['nw_firm_id'] = $request->query('nw_firm_id');
        }
        if ($request->has('platform')) {
            $biParam['platform'] = $request->query('platform');
        }
        if ($request->has('sdk_version_list')) {
            $biParam['sdk_version_list'] = $request->query('sdk_version_list');
        }
        if ($request->has('app_version_list')) {
            $biParam['app_version_list'] = $request->query('app_version_list');
        }

        /* 开发者 ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $publisherIdList = ReportInputFilter::getPublisherIdsByFilters(
            $request->query('publisher_id_list', []),
            $request->query('publisher_type', -1),
            $request->query('channel', -1),
            $request->query('publisher_id_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($publisherIdList)) {
            $biParam['publisher_id_list'] = $publisherIdList;
        }

        /* APP ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $appIdList = ReportInputFilter::getAppIdsByUuids(
            $request->query('app_uuid_list', []),
            $request->query('app_uuid_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        /* 获取绑定指定应用类型的 APP ID 列表 */
        $appIdListByType  = ReportInputFilter::getAppIdsByTermIds(
            $request->query('app_type_id_list', []),
            $request->query('app_type_id_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        /* 获取绑定指定应用标签的 APP ID 列表 */
        $appIdListByLabel = ReportInputFilter::getAppIdsByTermIds(
            $request->query('app_label_id_list', []),
            $request->query('app_label_id_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        /* 整合三个 APP ID 列表 */
        $appIdList = ReportInputFilter::integrateTwoList($appIdList, $appIdListByType);
        $appIdList = ReportInputFilter::integrateTwoList($appIdList, $appIdListByLabel);
        if (!empty($appIdList)) {
            $biParam['app_id_list'] = $appIdList;
        }

        /* Placement ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $placementIdList = ReportInputFilter::getPlacementIdsByUuids(
            $request->query('placement_uuid_list', []),
            $request->query('placement_uuid_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($placementIdList)) {
            $biParam['placement_id_list'] = $placementIdList;
        }

        /* 广告场景 ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $scenarioList = ReportInputFilter::getScenarioIdsByUuids(
            $request->query('scenario_uuid_list', []),
            $request->query('scenario_uuid_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($scenarioList)) {
            $biParam['scenario_list'] = $scenarioList;
        }

        /* 广告源 ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $adSourceIdList = ReportInputFilter::getUnitIdsByIds(
            $request->query('unit_id_list', []),
            $request->query('unit_id_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($adSourceIdList)) {
            $biParam['ad_source_id_list'] = $adSourceIdList;
        }

        /* 流量分组 ID 列表，非必填，若过滤后结果为空，直接返回空数据 */
        $segmentIdList = ReportInputFilter::getSegmentIdsByUuids(
            $request->query('segment_id_list', []),
            $request->query('segment_id_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($segmentIdList)) {
            $biParam['segment_id_list'] = $segmentIdList;
        }

        /* 地区缩写列表，非必填，若过滤后结果为空，直接返回空数据 */
        $geoShortList = ReportInputFilter::getGeoIdsByIds(
            $request->query('geo_short_list', []),
            $request->query('geo_short_list_exclude', Base::EXPORT_NOT) == Base::EXPORT_YES
        );
        if (!empty($geoShortList)) {
            $biParam['geo_short'] = $geoShortList;
        }

        $emptyRes =
            $publisherIdList === false ||
            $appIdList       === false ||
            $placementIdList === false ||
            $scenarioList    === false ||
            $adSourceIdList  === false ||
            $segmentIdList   === false ||
            $geoShortList    === false;

        return $biParam;
    }

    /**
     * @param array $param
     */
    public static function getBIData(array $param)
    {

    }

    public static function BIDataToViewModel(array $param)
    {

    }

}