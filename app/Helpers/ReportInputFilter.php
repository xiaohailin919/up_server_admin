<?php
namespace App\Helpers;

use App\Models\MySql\App;
use App\Models\MySql\AppTermRelationship;
use App\Models\MySql\Network;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Segment;
use App\Models\MySql\Scenario;
use App\Models\MySql\Geo;
use App\Models\MySql\Unit;
use DB;


class ReportInputFilter {

    /**
     * 获取该字段中非列表中的值
     *
     * @param array $list 待排除的值列表
     * @param string $table 表名
     * @param string $field 字段名
     * @param mixed $emptyResult 返回列表为空时返回的结果
     * @return array
     */
    public static function getExcludeValues(array $list, string $table, string $field, $emptyResult = []): array
    {
        $data = DB::query()->from($table)->whereNotIn($field, $list)->get([$field])->toArray();

        return empty($data) ? $emptyResult : array_column($data, $field);
    }

    /**
     * 获取App 包含/不包含 IDs
     * 若 $isExclude 为 true，则返回 $uuids 之外的 App 记录的 id
     *
     * @param  array $appUuids uuid 数组
     * @param  bool  $isExclude 是否包含
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getAppIdsByUuids(array $appUuids, $isExclude = false) {
        if(empty($appUuids)) {
            return [];
        }

        $query = App::query()->where('status', '!=', App::STATUS_DELETED);

        /* 包含与不包含 */
        $query = $isExclude ? $query->whereNotIn('uuid', $appUuids) : $query->whereIn('uuid', $appUuids);

        $apps = $query->get(['id'])->toArray();

        return empty($apps) ? false : array_column($apps, 'id');
    }

    /**
     * 根据 AppUuid 和 AppName 获取 app id
     *
     * @param array $appUuids
     * @param mixed $appName
     * @param bool $isExclude
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getAppIdsByUuidsAndName(array $appUuids, $appName = '', $isExclude = false) {
        if (empty($appUuids) && $appName == '') {
            return [];
        }

        $query = App::query()->where('status', '!=', App::STATUS_DELETED);

        if (!empty($appUuids)) {
            $query = $isExclude ? $query->whereNotIn('uuid', $appUuids) : $query->whereIn('uuid', $appUuids);
        }
        if (!empty($appName)) {
            $query->where('name', 'like', '%' . $appName . '%');
        }
        $apps = $query->get(['id'])->toArray();
        return empty($apps) ? false : array_column($apps, 'id');
    }

    /**
     * 根据 UUID、名称、开发者类型、渠道获取符合条件的开发者 ID
     *
     * @param array $publisherIds
     * @param mixed $publisherName
     * @param mixed $publisherType
     * @param mixed $channel
     * @param bool $isExclude
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getPublisherIdsByFilters(array $publisherIds, $publisherName = '', $publisherType = 'all', $channel = 'all', $isExclude = false) {
        if (empty($publisherIds) && $publisherName == '' && $publisherType == 'all' && $channel == 'all') {
            return [];
        }

        $query = Publisher::query()->where('status', '!=', Publisher::STATUS_DELETED);

        if (!empty($publisherIds)) {
            $query = $isExclude ? $query->whereNotIn('id', $publisherIds) : $query->whereIn('id', $publisherIds);
        }
        if ($publisherName != '') {
            $query->where('name', 'like', '%' . $publisherName . '%');
        }
        if(in_array($publisherType, [1, 2], false)){
            $query->where('mode', $publisherType);
        }
        if(in_array($channel, [0, 10000, 10001], false)){
            $query->where('channel_id', $channel);
        }
        $publishers = $query->get()->toArray();

        return empty($publishers) ? false : array_column($publishers, 'id');
    }

    /**
     * 获取 Placement 包含/不包含 IDs
     *
     * @param  array $placementUuids uuid 数组
     * @param  bool  $isExclude  是否包含
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getPlacementIdsByUuids(array $placementUuids, $isExclude = false) {
        if(empty($placementUuids)){
            return [];
        }

        $query = Placement::query()->where('status','!=',Placement::STATUS_DELETED);

        $query = $isExclude ? $query->whereNotIn('uuid', $placementUuids) : $query->whereIn('uuid', $placementUuids);

        $placements = $query->get(['id'])->toArray();

        return empty($placements) ? false : array_column($placements,'id');
    }

    /**
     * 获取Unit 包含/不包含 IDs
     *
     * @param  array $ids
     * @param  bool  $isExclude
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getUnitIdsByIds(array $ids, $isExclude = false){
        if(empty($ids)){
            return [];
        }

        $query = Unit::query()->where('status', '>', Unit::STATUS_DELETED);

        $query = $isExclude ? $query->whereNotIn('id', $ids) : $query->whereIn('id', $ids);

        $units = $query->get()->toArray();

        return empty($units) ? false : array_column($units, 'id');
    }

    /**
     * 获取Network 包含/不包含 IDs
     *
     * @param  array $ids
     * @param  bool  $isExclude
     * @return array|bool
     */
    public static function getNetworkIdsByIds(array $ids, $isExclude = false){
        if(empty($ids)){
            return [];
        }

        $query = Network::query()->where('status', '>', Network::STATUS_DELETED);

        $query = $isExclude ? $query->whereNotIn('id', $ids) : $query->whereIn('id', $ids);

        $networks = $query->get()->toArray();

        return empty($networks) ? false : array_column($networks, 'id');
    }

    /**
     * 获取Segment 包含/不包含 IDs
     *
     * @param  array  $segmentUuids uuid 数组
     * @param  bool  $isExclude  是否包含
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getSegmentIdsByUuids(array $segmentUuids, $isExclude = false)
    {
        if(empty($segmentUuids)){
            return [];
        }

        $query = Segment::query()->where('status', '!=', Segment::STATUS_BLOCKED);

        $query = $isExclude ? $query->whereNotIn('uuid', $segmentUuids) : $query->whereIn('uuid', $segmentUuids);

        $segments = $query->get(['id'])->toArray();

        return empty($segments) ? false : array_column($segments, 'id');
    }

    /**
     * 获取Scenario 包含/不包含 IDs
     *
     * @param  array  $uuids uuid 数组
     * @param  bool  $isExclude  是否包含
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getScenarioIdsByUuids(array $uuids, $isExclude = false) {
        if(empty($uuids)){
            return [];
        }

        $query = Scenario::query()->where('status', '!=',Scenario::STATUS_BLOCKED);

        $query = $isExclude ? $query->whereNotIn('uuid', $uuids) : $query->whereIn('uuid', $uuids);

        $scenarios = $query->get(['id'])->toArray();

        return empty($scenarios) ? false : array_column($scenarios, 'id');
    }

    /**
     * 获取 Geo 包含/不包含 IDs
     *
     * @param  array $ids
     * @param  bool  $isExclude
     * @return array|bool 若搜索 id 数组为空，则返回 false
     */
    public static function getGeoIdsByIds(array $ids, $isExclude = false) {
        if(empty($ids)){
            return [];
        }

        $query = Geo::query()->where('id', '<', 272)->where('code_id', '>', 0);

        $query = $isExclude ? $query->whereNotIn('short', $ids) : $query->whereIn('short', $ids);

        $areas = $query->get(['short'])->toArray();

        return empty($areas) ? false : array_column($areas, 'short');
    }

    /**
     * 根据 base64 编码的应用分类 ID，获取分类列表
     *
     * @param array $ids
     * @param false $isExclude
     * @return array|false
     */
    public static function getCategoriesByBase64Ids(array $ids, $isExclude = false) {
        if (empty($ids)) {
            return [];
        }

        /* Base64 解码原 ID 列表 */
        $categories = [];
        foreach ($ids as $id) {
            $tmpCategory = base64_decode($id);
            $categories[] = substr($tmpCategory, strpos($tmpCategory, "___") + 3);
        }

        $allCategories = App::query()->get(['category_2'])->toArray();
        $allCategories = array_values(array_unique(array_column($allCategories, 'category_2')));


        if ($isExclude) {
            foreach ($allCategories as $key => $category) {
                if (in_array($category, $categories, false)) {
                    unset($allCategories[$key]);
                }
            }
            $categories = array_values($allCategories);
        }

        return $categories;
    }

    /**
     * 根据 Type ID 或 Label ID 搜符合条件的 App ID
     *
     * @param $typeIds
     * @param $isExclude
     * @return array|false
     */
    public static function getAppIdsByTermIds(array $typeIds, $isExclude) {
        if (empty($typeIds)) {
            return [];
        }

        $relationships = AppTermRelationship::query()
            ->whereIn('term_id', $typeIds)
            ->groupBy(['app_id'])
            ->get(['app_id'])->toArray();
        $relationships = array_column($relationships, 'app_id');

        /* 如果是没有该标签或类型的 app，应该拿所有 app 来筛 */
        if ($isExclude) {
            $allAppIds = App::query()->orderByDesc('id')->get(['id'])->toArray();
            $allAppIds = array_column($allAppIds, 'id');
            $relationships = array_unique(array_values(array_diff($allAppIds, $relationships)), SORT_NUMERIC);
        }
        return empty($relationships) ? false : $relationships;
    }

//    /**

//     * 获取Product 包含/不包含 IDs
    /**
     * 根据应用类型或应用标签搜索 App
     * 已经过验证
     *
     * 逻辑如下，设 typeIds 直接查询的 APP 为 A，labelIds 直接查询的 APP 为 B：
     *
     * A(include) + B(include) => A ∩ B
     * A(include) + B(exclude) => A - B
     * A(exclude) + B(include) => B - A
     * A(exclude) + B(exclude) => U - (A ∪ B)
     *
     * typeIds 或 labelIds 为空表示该查询范围为 []，因此：
     * [] + include = [],
     * [] + exclude = U
     *
     * @param array $typeIds
     * @param $typeExclude
     * @param array $labelIds
     * @param $labelExclude
     * @return array
     */
    public static function getAppIdListByAppTerm(array $typeIds, $typeExclude, array $labelIds, $labelExclude)
    {
        /* 自联表 */
        $query = DB::query()
            ->from(AppTermRelationship::TABLE . ' as t1')
            ->join(AppTermRelationship::TABLE . ' as t2', 't1.app_id', '=', 't2.app_id')
            ->groupBy(['t1.app_id']);

        if ($typeExclude && $labelExclude) {
            /* 全集减去二者并集 */
            $universe = array_column(App::query()->get(['id'])->toArray(), 'id');
            $query->whereIn('t1.term_id', $typeIds)->orWhereIn('t2.term_id', $labelIds);
            $union = array_column($query->get(['t1.app_id'])->toArray(), 'app_id');
            $appIds = array_values(array_diff($universe, $union));
        } else {
            if ($typeExclude) {
                /* Label APP 集减去 Type APP 集 */
                $filtrateAppIds = array_column(AppTermRelationship::query()->whereIn('term_id', $typeIds)->get(['app_id'])->toArray(), 'app_id');
                $query->whereIn('t1.term_id', $labelIds)->whereNotIn('t1.app_id', $filtrateAppIds);
            } else if ($labelExclude) {
                /* Type APP 集减去 Label APP 集 */
                $filtrateAppIds = array_column(AppTermRelationship::query()->whereIn('term_id', $labelIds)->get(['app_id'])->toArray(), 'app_id');
                $query->whereIn('t1.term_id', $typeIds)->whereNotIn('t1.app_id', $filtrateAppIds);
            } else {
                /* 取二者交集 */
                $query->whereIn('t1.term_id', $typeIds)->whereIn('t2.term_id', $labelIds);
            }
            $appIds = array_column($query->get(['t1.app_id'])->toArray(), 'app_id');
        }

        return $appIds;
    }

//     *
//     * @param  array $uuids
//     * @param  bool  $isExclude
//     * @return array|bool 若搜索 id 数组为空，则返回 false
//     */
//    public static function getProductIdsByUuids(array $uuids, $isExclude = false) {
//        if(empty($uuids)){
//            return [];
//        }
//
//        $query = Product::select('id')
//            ->where('publisher_id', AuthUser::id())
//            ->where('status', '>', Product::STATUS_DELETED);
//        if($isExclude){
//            $query->whereNotIn('uuid', $uuids);
//        }else{
//            $query->whereIn('uuid', $uuids);
//        }
//
//        $product = $query->get()->toArray();
//        if(empty($product)){
//            return false;
//        }
//        $ids = array_column($product, 'id');
//
//        return $ids;
//    }
//
//    /**
//     * 获取Offer 包含/不包含 IDs
//     *
//     * @param  array $uuids
//     * @param  bool  $isExclude
//     * @return array|bool
//     */
//    public static function getOfferIdsByUuids(array $uuids, $isExclude = false){
//        if(empty($uuids)){
//            return [];
//        }
//
//        $query = Offer::select('id')
//            ->where('publisher_id', AuthUser::id())
//            ->where('status', '>', Offer::STATUS_DELETED);
//        if($isExclude){
//            $query->whereNotIn('uuid', $uuids);
//        }else{
//            $query->whereIn('uuid', $uuids);
//        }
//
//        $offer = $query->get()->toArray();
//        if(empty($offer)){
//            return false;
//        }
//        $ids = array_column($offer, 'id');
//
//        return $ids;
//    }
}