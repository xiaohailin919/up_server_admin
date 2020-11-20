<?php

namespace App\Http\Controllers;

use App\Helpers\Database;
use App\Helpers\Export;
use App\Helpers\ReportInputFilter;
use App\Models\MySql\Publisher;
use App\Services\App as AppService;
use App\Models\MySql\App;
use App\Models\MySql\AppTerm;
use App\Models\MySql\Base;
use App\Models\MySql\DataSortMetrics;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends ApiController
{
    /**
     * 应用 - 列表
     * 接口文档：http://192.168.86.20:3000/project/18/interface/api/32
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        /* 收益报表更新时间 */
        $metricsDataUpdateTime = DataSortMetrics::query()->orderByDesc('id')->value('create_time');

        /*
          由于最终需要按照 revenue 或者 dau 排，因此在无搜索条件的前提下联表必定会让 app 表进行全表扫描，再与中间表的数据进行过滤连接，非常耗时；
          同时搜索条件不包括中间表条件，因此可拆分成两步走：
          1. 搜索全部符合条件的 AppID
          2. 利用 APP ID，以中间表为主表联 APP 表，搜出需要的数据
        */
        $query = App::query()
            ->from(App::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->select(['t1.id'])->orderBy('t1.id');

        if ($request->has('name')) {
            $query->where('t1.name', 'like', '%' . $request->query('name') . '%');
        }
        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if ($request->has('publisher_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('publisher_name') . '%');
        }
        if ($request->has('uuid')) {
            $tmp = $request->query('uuid');
            $query->where(static function($subQuery) use ($tmp) {
                $subQuery->where('t1.id', $tmp)->orWhere('t1.uuid', $tmp);
            });
        }
        if ($request->has('package')) {
            $tmp = $request->query('package');
            $query->where(static function($subQuery) use ($tmp) {
                $subQuery->where('t1.platform_app_id', $tmp)->orWhere('t1.bundle_id', $tmp);
            });
        }
        if (array_key_exists($request->query('platform', -1), App::getPlatformMap())) {
            $query->where('t1.platform', $request->query('platform'));
        }
        if (array_key_exists($request->query('status', -1), App::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }
        if ($request->has('category_list')) {
            $categories = ReportInputFilter::getCategoriesByBase64Ids($request->query('category_list'), $request->query('category_list_exclude', 1) == 2);
            $query->whereIn('t1.category_2', $categories);
        }
        $typeAppIds = $labelAppIds = [];
        if ($request->has('type_id_list')) {
            $typeAppIds = ReportInputFilter::getAppIdsByTermIds($request->query('type_id_list'), $request->query('type_id_list_exclude', 1) == 2);
        }
        if ($request->has('label_id_list')) {
            $labelAppIds = ReportInputFilter::getAppIdsByTermIds($request->query('label_id_list'), $request->query('label_id_list_exclude', 1) == 2);
        }
        /* 合并两个过滤条件，有一个 false 则返回空结果，两个空数组则返回所有，默认搜所有 */
        if ($labelAppIds === false || $typeAppIds === false) {
            return $this->jsonResponse(['total' => 0, 'list' => [], 'page_no' => $request->query('page_no', 1), 'page_size' => $request->query('page_size', 10), 'metrics_data_update_time' => $metricsDataUpdateTime]);
        }
        if ($labelAppIds != [] && $typeAppIds != []) {
            $query->whereIn('t1.id', array_intersect($labelAppIds, $typeAppIds));
        } else if ($labelAppIds !== [] || $typeAppIds !== []) {
            $query->whereIn('t1.id', array_merge($labelAppIds, $typeAppIds));
        }
        $appIds = $query->get()->getQueueableIds();

        /* 如果是导出报表，则默认第一页，10000 数据 */
        $limit = $request->query('is_export', Base::EXPORT_NOT) == Base::EXPORT_YES
            ? 10000 : $request->query('page_size', 10);
        $offset = $request->query('is_export', Base::EXPORT_NOT) == Base::EXPORT_YES
            ? 1 : ($request->query('page_no', 1) - 1) * $limit;

        $data = [
            'total'                    => count($appIds),
            'page_no'                  => $offset / $limit + 1,
            'page_size'                => $limit,
            'metrics_data_update_time' => $metricsDataUpdateTime,
        ];

        /* 获取收益、Dau 数据 */
        $extraData = DataSortMetrics::query()->select(['app_id', 'revenue', 'dau'])
            ->where('type', DataSortMetrics::TYPE_APP)
            ->where('period_type', $request->query('date_type', DataSortMetrics::PERIOD_TYPE_WEEK_ONE_DAY_AGO))
            ->whereIn('app_id', $appIds)
            ->orderBy($request->query('order_by_field', 'revenue'), $request->query('order_by_direction', 'desc'))
            ->offset($offset)->limit($limit)
            ->get()->toArray();
        $extraDataMap = [];
        foreach ($extraData as $extraDatum) {
            $extraDataMap[$extraDatum['app_id']] = $extraDatum;
        }

//        return $this->jsonResponse($extraData);

        /* 更新为最终的 appId：1.如果数量满足 limit，则直接用，否则补记录到满足数量 */
        if (count($extraData) < $limit) {
            $tmpArray = $appIds;
            $appIds = array_column($extraData, 'app_id');
            for ($i = 0, $iMax = count($tmpArray); $i < $iMax && count($appIds) < $limit; $i++) {
                if (!in_array($tmpArray[$i], $appIds, true)) {
                    $appIds[] = $tmpArray[$i];
                }
            }
        } else {
            $appIds = array_column($extraData, 'app_id');
        }

//        return $this->jsonResponse($appIds);

        /* 获取 App 相关数据 */
        $appData = App::query()
            ->from(App::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->whereIn('t1.id', $appIds)
            ->select(
                't1.id', 't1.uuid', 't1.name', 't1.platform', 't1.platform_app_id as package', 't1.bundle_id', 't1.publisher_id',
                't2.name as publisher_name', 't1.category', 't1.category_2', 't1.status', 't1.private_status',
                't1.update_time', 't1.create_time'
            )->get()->toArray();
        $appMap = [];
        foreach ($appData as $appDatum) {
            $appMap[$appDatum['id']] = $appDatum;
        }

        /* 获取应用标签相关数据 */
        $appTermData = AppTerm::query()->leftJoin('app_term_relationship as t2', 't2.term_id', '=', 'app_term.id')
            ->whereIn('t2.app_id', $appIds)
            ->get(['app_term.id', 'app_term.type', 'app_term.name', 'parent_id', 't2.app_id'])->toArray();
        $appTypeMap = [];
        $appLabelListMap = [];
        $appParentLabelIdMap = [];
        foreach ($appTermData as $appTermDatum) {
            if ($appTermDatum['type'] == AppTerm::TYPE_APP_TYPE) {
                $appTypeMap[$appTermDatum['app_id']] = $appTermDatum;
            } else {
                $appParentLabelIdMap[$appTermDatum['app_id']] = $appTermDatum['parent_id'];
                $appLabelListMap[$appTermDatum['app_id']][] = [
                    'value' => $appTermDatum['id'],
                    'label' => $appTermDatum['name'],
                ];
            }
        }

        /* 获取当前用户可操作的 publisher id 列表 */
        $operablePublisherIds = UserService::getPublisherIdListByUserId(auth()->id());

        /* 拼数据，并排序 */
        $list = [];
        foreach ($appIds as $appId) {
            $app = $appMap[$appId];
            $app['revenue']         = $extraDataMap[$appId]['revenue'] ?? '';
            $app['dau']             = $extraDataMap[$appId]['dau']     ?? '';
            $app['type_id']         = $appTypeMap[$appId]['id']        ?? '';
            $app['type_name']       = $appTypeMap[$appId]['name']      ?? '';
            $app['label_parent_id'] = $appParentLabelIdMap[$appId]     ?? 0;
            $app['label_list']      = $appLabelListMap[$appId]         ?? [];

            /* 数据权限标识与数据过滤 */
            $app['operable'] = 1;
            if (!in_array($app['publisher_id'], $operablePublisherIds, false)) {
                $app['operable'] = 2;
                $app['revenue']  = '';
                $app['dau']      = '';
            }

            $list[] = $app;
        }
        $data['list'] = $list;

        /* 非导出报表，正常返回 */
        if ($request->query('is_export', Base::EXPORT_NOT) == Base::EXPORT_NOT) {
            return $this->jsonResponse($data);
        }

        $exportHeader = AppService::generateExportHeader();
        $exportData = AppService::generateExportData($data['list']);

        Export::exportAsCsv($exportData, $exportHeader, 'app_' . date('Ymd_His') . '.csv');
    }

    /**
     * App 的应用类型、应用标签、应用分类列表
     * 接口文档地址：http://192.168.86.20:3000/project/18/interface/api/140
     *
     * @return JsonResponse
     */
    public function termList(): JsonResponse
    {
        $resData = ['type_list' => [], 'label_list' => [], 'category_list' => []];

        /* 处理引用类型列表和应用标签列表 */
        $appTerms = AppTerm::query()->where('status', AppTerm::STATUS_ACTIVE)->get();
        $secondLabelList = [];
        foreach ($appTerms as $appTerm) {
            switch ($appTerm['type']) {
                case AppTerm::TYPE_APP_TYPE:
                    $resData['type_list'][] = ['value' => $appTerm['id'], 'label' => $appTerm['name']];
                    break;
                case AppTerm::TYPE_LABEL_PARENT:
                    $resData['label_list'][] = ['value' => $appTerm['id'], 'label' => $appTerm['name']];
                    break;
                case AppTerm::TYPE_LABEL_CHILD:
                    $secondLabelList[$appTerm['parent_id']][] = ['value' => $appTerm['id'], 'label' => $appTerm['name']];
            }
        }
        foreach ($secondLabelList as $parent_id => $items) {
            foreach ($resData['label_list'] as $key => $firstLabel) {
                if ($firstLabel['value'] === $parent_id) {
                    foreach ($items as $item) {
                        $firstLabel['children'][] = $item;
                    }
                }
                $resData['label_list'][$key] = $firstLabel;
            }
        }

        /* 处理应用分类列表 */
        $resData['category_list'] = App::getAllPlatformCategoryMap();

        return $this->jsonResponse($resData);
    }
    
//    public function update(Request $request)
//    {
////        $this->checkAccessPermission('app_edit');
//
//        $id = $request->input('id', 0);
//        $privateStatus = $request->input('private_status', 1);
//        $type = $request->input('type', 'private_status');
//
//        $appMyModel = new AppMyModel();
//        if ($type == 'private_status') {
//            $appMyModel->updatePrivateStatus($id, $privateStatus);
//            (new PlacementMyModel())->updatePrivateStatusByAppId($id, $privateStatus);
//        }
//
//        //触发更新app的事件
//        Event::fire(new UpdateApp($id));
//
//        return ["status" => 1];
//    }

    /**
     * 获取 APP 列表的时间筛选框数据
     */
    public function getTimeMap(): JsonResponse {

        $periodTypeMap = DataSortMetrics::getPeriodTypeMap();
        $res = [];
        foreach ($periodTypeMap as $periodType => $name) {
            $res[] = ['id' => $periodType, 'name' => $name];
        }
        return $this->jsonResponse($res);
    }
}
