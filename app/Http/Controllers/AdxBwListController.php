<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\MySql\Users;
use App\Models\MySql\AdxBwList;
use App\Models\MySql\App;

use App\Helpers\InputFilter;
use App\Helpers\TimeConversion;

class AdxBwListController extends ApiController
{
    private $dimNeedPublisherId = [
        AdxBwList::DIMENSION_PUBLISHER,
        AdxBwList::DIMENSION_PUBLISHER_AREA,
        AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
        AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
    ];
    private $dimNeedPlatform = [
        AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
        AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
    ];
    private $dimNeedAppId = [
        AdxBwList::DIMENSION_APP,
        AdxBwList::DIMENSION_APP_AREA
    ];
    private $dimNeedGeoShort = [
        AdxBwList::DIMENSION_PUBLISHER_AREA,
        AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA,
        AdxBwList::DIMENSION_APP_AREA
    ];

    /**
     * ADX 黑/白名单 列表
     * http://yapi.toponad.com/project/18/interface/api/689
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $dimension = (int)$request->input('dimension', 0);
        $name = $request->input('name', '');
        $publisherId = (int)$request->input('publisher_id', 0);
        $appUuid = $request->input('app_id', '');
        $geoShort = $request->input('geo_short', '');
        $pageSize = (int)$request->get('page_size', 10);
        $pageNo = (int)$request->get('page_no', 1);

        $query = AdxBwList::query()
            ->where('parent_id', 0)
            ->where('status', '<', AdxBwList::STATUS_DELETE);
        if ($dimension > 0) {
            $query->where('dimension', $dimension);
        }
        if (!empty($name)) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($publisherId > 0) {
            $query->where('publisher_id', 'like', "%{$publisherId}%");
        }
        if (!empty($appUuid)) {
            $query->where('app_id', 'like', "%{$appUuid}%");
        }
        if (!empty($geoShort)) {
            $query->where('geo_short', 'like', "%{$geoShort}%");
        }

        $selectList = ['id', 'type', 'dimension', 'name', 'publisher_id', 'app_id', 'geo_short',
            'admin_id', 'create_time', 'update_time', 'status'];
        $paginator = $query->orderBy('dimension', 'desc')
            ->orderBy('update_time', 'desc')
            ->paginate($pageSize, $selectList, 'page_no', $pageNo);
        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as &$val) {
            $val['publisher_id_list'] = json_decode($val['publisher_id'], true);
            $val['app_id_list']       = json_decode($val['app_id'], true);
            $val['geo_short_list']    = json_decode($val['geo_short'], true);
            $val['admin_name']  = (new Users())->getName($val['admin_id']);
            $val['create_time'] = TimeConversion::dateTimeToMsTimestamp($val['create_time']);
            $val['update_time'] = TimeConversion::dateTimeToMsTimestamp($val['update_time']);
        }

        return $this->jsonResponse($data);
    }

    /**
     * ADX 黑/白名单 新增
     * http://yapi.toponad.com/project/18/interface/api/707
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // 表单校验前清洗数据
        $appIds       = (array)$request->input('app_id_list', []);
        $appIds       = InputFilter::getAppUuidsByUuids($appIds);
        $publisherIds = (array)$request->input('publisher_id_list', []);
        $publisherIds = InputFilter::getPublisherIdsByIds($publisherIds);
        $geoShorts    = (array)$request->input('geo_short_list', []);
        $geoShorts    = InputFilter::getGeoShrotsByShorts($geoShorts);
        $request->merge([
            'app_id'       => $appIds,
            'publisher_id' => $publisherIds,
            'geo_short'    => $geoShorts
        ]);

        // 表单校验
        $rules = [
            'name' => ['required', 'string'],
            'dimension' => ['required', 'integer', Rule::in(array_keys(AdxBwList::getDimensionMap()))],
            'publisher_id' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
            ],
            'platform' => ['integer',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
            ],
            'app_id' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_APP,
                'required_if:dimension,' . AdxBwList::DIMENSION_APP_AREA
            ],
            'geo_short' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_APP_AREA
            ],
            'demand_switch'               => ['required', 'integer', Rule::in([1, 2])],
            'demand_list'                 => ['required_if:demand_switch,2', 'array'],
//            'nw_firm_online_api_list'     => ['present', 'array'],
//            'nw_firm_online_api_list.*'   => ['exists:network_firm,id'],
            'package_name_ios_switch'     => ['required', 'integer', Rule::in([1, 2])],
            'package_name_ios_list'       => ['required_if:package_name_ios_switch,2', 'array'],
            'package_name_android_switch' => ['required', 'integer', Rule::in([1, 2])],
            'package_name_android_list'   => ['required_if:package_name_android_switch,2', 'array'],
            'category_switch'             => ['required', 'integer', Rule::in([1, 2])],
            'category_list'               => ['required_if:category_switch,2', 'array'],
            'adv_domain_switch'           => ['required', 'integer', Rule::in([1, 2])],
            'adv_domain_list'             => ['required_if:adv_domain_switch,2', 'array'],
        ];
        Validator::make($request->all(), $rules)->validate();

        $adxBwList = $request->only([
            'name', 'dimension', 'publisher_id', 'platform', 'app_id', 'geo_short',
        ]);

        $input = $request->input();
        // 校验名称重复
        $exists = AdxBwList::query()
            ->where('name', $input['name'])
            ->where('parent_id', 0)
            ->where('status', '<', AdxBwList::STATUS_DELETE)
            ->exists();
        if($exists){
            return $this->jsonResponse(['name' => '名称重复'], 10000, '', 422);
        }
        $dimension = $input['dimension'];

        $adxBwList['demand'] = [];
        if($input['demand_switch'] == 2){
            $adxBwList['demand'] = $input['demand_list'];
        }
//        $adxBwList['network_firm'] = $input['nw_firm_online_api_list'];
        $adxBwList['package_name_ios'] = [];
        if($input['package_name_ios_switch'] == 2){
            $adxBwList['package_name_ios'] = $input['package_name_ios_list'];
        }
        $adxBwList['package_name_android'] = [];
        if($input['package_name_android_switch'] == 2){
            $adxBwList['package_name_android'] = $input['package_name_android_list'];
        }
        $adxBwList['category'] = [];
        if($input['category_switch'] == 2){
            $adxBwList['category'] = $input['category_list'];
        }
        $adxBwList['adv_domain'] = [];
        if($input['adv_domain_switch'] == 2){
            $adxBwList['adv_domain'] = $input['adv_domain_list'];
        }

        if(in_array($dimension, $this->dimNeedPublisherId)){
            $adxBwList['publisher_id'] = json_encode((array)data_get($adxBwList, 'publisher_id', []));
        }else{
            $adxBwList['publisher_id'] = '[]';
        }
        if(in_array($dimension, $this->dimNeedPlatform)){
            $adxBwList['platform'] = json_encode((array)data_get($adxBwList, 'platform', []));
        }else{
            $adxBwList['platform'] = '[]';
        }
        if(in_array($dimension, $this->dimNeedAppId)){
            $adxBwList['app_id'] = json_encode((array)data_get($adxBwList, 'app_id', []));
        }else{
            $adxBwList['app_id'] = '[]';
        }
        if(in_array($dimension, $this->dimNeedGeoShort)){
            $adxBwList['geo_short'] = json_encode((array)data_get($adxBwList, 'geo_short', []));
        }else{
            $adxBwList['geo_short'] = '[]';
        }
        $adxBwList['demand']               = json_encode((array)data_get($adxBwList, 'demand', []));
//        $adxBwList['network_firm']         = json_encode((array)data_get($adxBwList, 'network_firm', []));
        $adxBwList['package_name_ios']     = json_encode((array)data_get($adxBwList, 'package_name_ios', []));
        $adxBwList['package_name_android'] = json_encode((array)data_get($adxBwList, 'package_name_android', []));
        $adxBwList['category']             = json_encode((array)data_get($adxBwList, 'category', []));
        $adxBwList['adv_domain']           = json_encode((array)data_get($adxBwList, 'adv_domain', []));
        $adxBwList['admin_id']             = Auth::id();

        // 检查数据是否重复
        $adxBwListModel = new AdxBwList();
        $dimensions = $adxBwListModel->generateDimensions($adxBwList);
        $checkDimensions = $adxBwListModel->checkDimensions($dimensions);
        if(!empty($checkDimensions)){
            $msg = '以下配置已存在，请检查后重新提交。';
            foreach($checkDimensions as $val){
                if($val['publisher_id'] > 0){
                    $msg .= "Publisher ID: {$val['publisher_id']} ";
                }
                if($val['platform'] > 0){
                    $msg .= 'Platform: ' . ($val['platform'] == 2 ? 'iOS' : 'Android') . ' ';
                }
                if($val['app_id'] > 0){
                    $msg .= 'App ID: ' . (new App())->getUuid($val['app_id']) . ' ';
                }
                if(!empty($val['geo_short'])){
                    $msg .= 'Area: ' . $val['geo_short'] . ' ';
                }
                $msg .= "; ";
            }
            return $this->jsonResponse($checkDimensions, 10000, $msg);
        }

        $result = AdxBwList::query()->create($adxBwList);
        (new AdxBwList())->saveChildren($result->toArray(), $dimensions);

        return $this->jsonResponse();
    }

    /**
     * ADX 黑/白名单 单条记录
     * http://yapi.toponad.com/project/18/interface/api/698
     *
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $data = AdxBwList::query()
            ->select(
                'id', 'type', 'dimension', 'name', 'admin_id', 'create_time', 'update_time', 'status',
                'publisher_id         as publisher_id_list',
                'app_id               as app_id_list',
                'geo_short            as geo_short_list',
                'demand               as demand_list',
//                'network_firm         as nw_firm_online_api_list',
                'package_name_ios     as package_name_ios_list',
                'package_name_android as package_name_android_list',
                'category             as category_list',
                'adv_domain           as adv_domain_list'
            )
            ->where('id', $id)
            ->first();

        $data->publisher_id_list         = json_decode($data->publisher_id_list, true);
        $data->app_id_list               = json_decode($data->app_id_list, true);
        $data->geo_short_list            = json_decode($data->geo_short_list, true);
        $data->demand_list               = json_decode($data->demand_list, true);
//        $data->nw_firm_online_api_list   = json_decode($data->nw_firm_online_api_list, true);
        $data->package_name_ios_list     = json_decode($data->package_name_ios_list, true);
        $data->package_name_android_list = json_decode($data->package_name_android_list, true);
        $data->category_list             = json_decode($data->category_list, true);
        $data->adv_domain_list           = json_decode($data->adv_domain_list, true);
        $data->create_time               = TimeConversion::dateTimeToMsTimestamp($data->create_time);
        $data->update_time               = TimeConversion::dateTimeToMsTimestamp($data->update_time);

        $data->demand_switch = 1;
        if(!empty($data->demand_list)){
            $data->demand_switch = 2;
        }
        $data->package_name_ios_switch = 1;
        if(!empty($data->package_name_ios_list)){
            $data->package_name_ios_switch = 2;
        }
        $data->package_name_android_switch = 1;
        if(!empty($data->package_name_android_list)){
            $data->package_name_android_switch = 2;
        }
        $data->category_switch = 1;
        if(!empty($data->category_list)){
            $data->category_switch = 2;
        }
        $data->adv_domain_switch = 1;
        if(!empty($data->adv_domain_list)){
            $data->adv_domain_switch = 2;
        }

        return $this->jsonResponse($data);
    }

    /**
     * ADX 黑/白名单 更新
     * http://yapi.toponad.com/project/18/interface/api/716
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        // 获取当前记录
        $adxBwList = AdxBwList::query()
            ->where('id', $id)
            ->first();

        // 表单校验前清洗数据
        $appIds       = (array)$request->input('app_id_list', []);
        $appIds       = InputFilter::getAppUuidsByUuids($appIds);
        $publisherIds = (array)$request->input('publisher_id_list', []);
        $publisherIds = InputFilter::getPublisherIdsByIds($publisherIds);
        $geoShorts    = (array)$request->input('geo_short_list', []);
        $geoShorts    = InputFilter::getGeoShrotsByShorts($geoShorts);
        $request->merge([
            'dimension' => $adxBwList->dimension,
            'app_id' => $appIds,
            'publisher_id' => $publisherIds,
            'geo_short' => $geoShorts
        ]);

        // 表单校验
        $rules = [
            'name' => ['required', 'string'],
            'dimension' => ['required', 'integer', Rule::in(array_keys(AdxBwList::getDimensionMap()))],
            'publisher_id' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
            ],
            'platform' => ['integer',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA
            ],
            'app_id' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_APP,
                'required_if:dimension,' . AdxBwList::DIMENSION_APP_AREA
            ],
            'geo_short' => ['array',
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_PUBLISHER_PLATFORM_AREA,
                'required_if:dimension,' . AdxBwList::DIMENSION_APP_AREA
            ],
            'demand_switch'               => ['required', 'integer', Rule::in([1, 2])],
            'demand_list'                 => ['required_if:demand_switch,2', 'array'],
//            'nw_firm_online_api_list'     => ['present', 'array'],
//            'nw_firm_online_api_list.*'   => ['exists:network_firm,id'],
            'package_name_ios_switch'     => ['required', 'integer', Rule::in([1, 2])],
            'package_name_ios_list'       => ['required_if:package_name_ios_switch,2', 'array'],
            'package_name_android_switch' => ['required', 'integer', Rule::in([1, 2])],
            'package_name_android_list'   => ['required_if:package_name_android_switch,2', 'array'],
            'category_switch'             => ['required', 'integer', Rule::in([1, 2])],
            'category_list'               => ['required_if:category_switch,2', 'array'],
            'adv_domain_switch'           => ['required', 'integer', Rule::in([1, 2])],
            'adv_domain_list'             => ['required_if:adv_domain_switch,2', 'array'],
        ];
        Validator::make($request->all(), $rules)->validate();

        $adxBwList->fill($request->only([
            'name', 'dimension', 'publisher_id', 'platform', 'app_id', 'geo_short',
        ]));

        $input = $request->input();
        // 校验名称重复
        $exists = AdxBwList::query()
            ->where('name', $input['name'])
            ->where('id', '!=', $id)
            ->where('parent_id', 0)
            ->where('status', '<', AdxBwList::STATUS_DELETE)
            ->exists();
        if($exists){
            return $this->jsonResponse(['name' => '名称重复'], 10000, '', 422);
        }
        $dimension = $input['dimension'];

        $adxBwList->demand = [];
        if($input['demand_switch'] == 2){
            $adxBwList->demand = $input['demand_list'];
        }
//        /* 若全部/不限，前端传空数组 */
//        $adxBwList->network_firm = $input['nw_firm_online_api_list'];
        $adxBwList->package_name_ios = [];
        if($input['package_name_ios_switch'] == 2){
            $adxBwList->package_name_ios = $input['package_name_ios_list'];
        }
        $adxBwList->package_name_android = [];
        if($input['package_name_android_switch'] == 2){
            $adxBwList->package_name_android = $input['package_name_android_list'];
        }
        $adxBwList->category = [];
        if($input['category_switch'] == 2){
            $adxBwList->category = $input['category_list'];
        }
        $adxBwList->adv_domain = [];
        if($input['adv_domain_switch'] == 2) {
            $adxBwList->adv_domain = $input['adv_domain_list'];
        }

        if(in_array($dimension, $this->dimNeedPublisherId)){
            $adxBwList->publisher_id = json_encode((array)$adxBwList->publisher_id);
        }else{
            $adxBwList->publisher_id = '[]';
        }
        if(in_array($dimension, $this->dimNeedPlatform)){
            $adxBwList->platform = json_encode((array)$adxBwList->platform);
        }else{
            $adxBwList->platform = '[]';
        }
        if(in_array($dimension, $this->dimNeedAppId)){
            $adxBwList->app_id = json_encode((array)$adxBwList->app_id);
        }else{
            $adxBwList->app_id = '[]';
        }
        if(in_array($dimension, $this->dimNeedGeoShort)){
            $adxBwList->geo_short = json_encode((array)$adxBwList->geo_short);
        }else{
            $adxBwList->geo_short = '[]';
        }

        $adxBwList->demand               = json_encode((array)$adxBwList->demand);
//        $adxBwList->network_firm         = json_encode((array)$adxBwList->network_firm);
        $adxBwList->package_name_ios     = json_encode((array)$adxBwList->package_name_ios);
        $adxBwList->package_name_android = json_encode((array)$adxBwList->package_name_android);
        $adxBwList->category             = json_encode((array)$adxBwList->category);
        $adxBwList->adv_domain           = json_encode((array)$adxBwList->adv_domain);
        $adxBwList->admin_id             = Auth::id();
        $adxBwList->update_time          = date('Y-m-d H:i:s');

        // 检查数据是否重复
        $adxBwListModel = new AdxBwList();
        $dimensions = $adxBwListModel->generateDimensions($adxBwList->toArray());
        $checkDimensions = $adxBwListModel->checkDimensions($dimensions, $id);
        if(!empty($checkDimensions)){
            $msg = '以下配置已存在，请检查后重新提交。';
            foreach($checkDimensions as $val){
                if($val['publisher_id'] > 0){
                    $msg .= "Publisher ID: {$val['publisher_id']} ";
                }
                if($val['platform'] > 0){
                    $msg .= 'Platform: ' . ($val['platform'] == 2 ? 'iOS' : 'Android') . ' ';
                }
                if($val['app_id'] > 0){
                    $msg .= 'App ID: ' . (new App())->getUuid($val['app_id']) . ' ';
                }
                if(!empty($val['geo_short'])){
                    $msg .= 'Area: ' . $val['geo_short'] . ' ';
                }
                $msg .= "; ";
            }
            return $this->jsonResponse($checkDimensions, 10000, $msg);
        }

        $adxBwList->update();
        $adxBwListModel->saveChildren($adxBwList->toArray(), $dimensions);

        return $this->jsonResponse();
    }

    /**
     * ADX管理 删除
     * http://yapi.toponad.com/project/18/interface/api/869
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 获取当前记录
        $adxBwList = AdxBwList::query()
            ->where('id', $id)
            ->first();
        $dateTime = date('Y-m-d H:i:s');
        // parent
        $adxBwList->status      = AdxBwList::STATUS_DELETE;
        $adxBwList->update_time = $dateTime;
        $adxBwList->admin_id    = Auth::id();
        $adxBwList->update();
        // children
        AdxBwList::query()
            ->where('parent_id', $id)
            ->update([
                'status'      => AdxBwList::STATUS_DELETE,
                'update_time' => $dateTime,
                'admin_id'    => Auth::id()
            ]);

        return $this->jsonResponse();
    }

    /**
     * IAB分类元数据
     * http://yapi.toponad.com/project/18/interface/api/842
     *
     * @return JsonResponse
     */
    public function category()
    {
        $data = [
            [
                'value' => 'IAB1',
                'label' => 'Arts & Entertainment',
                'children' => [
                    ['value' => 'IAB1-1', 'label' => 'Books & Literature'],
                    ['value' => 'IAB1-2', 'label' => 'Celebrity Fan/Gossip'],
                    ['value' => 'IAB1-3', 'label' => 'Fine Art'],
                    ['value' => 'IAB1-4', 'label' => 'Humor'],
                    ['value' => 'IAB1-5', 'label' => 'Movies'],
                    ['value' => 'IAB1-6', 'label' => 'Music'],
                    ['value' => 'IAB1-7', 'label' => 'Television'],
                ]
            ],

            ['value' => 'IAB2', 'label' => 'Automotive',
                'children' => [
                    ['value' => 'IAB2-1', 'label' => 'Auto Parts'],
                    ['value' => 'IAB2-2', 'label' => 'Auto Repair'],
                    ['value' => 'IAB2-3', 'label' => 'Buying/Selling Cars'],
                    ['value' => 'IAB2-4', 'label' => 'Car Culture'],
                    ['value' => 'IAB2-5', 'label' => 'Certified Pre-Owned'],
                    ['value' => 'IAB2-6', 'label' => 'Convertible'],
                    ['value' => 'IAB2-7', 'label' => 'Coupe'],
                    ['value' => 'IAB2-8', 'label' => 'Crossover'],
                    ['value' => 'IAB2-9', 'label' => 'Diesel'],
                    ['value' => 'IAB2-10', 'label' => 'Electric Vehicle'],
                    ['value' => 'IAB2-11', 'label' => 'Hatchback'],
                    ['value' => 'IAB2-12', 'label' => 'Hybrid'],
                    ['value' => 'IAB2-13', 'label' => 'Luxury'],
                    ['value' => 'IAB2-14', 'label' => 'Minivan'],
                    ['value' => 'IAB2-15', 'label' => 'Motorcycles'],
                    ['value' => 'IAB2-16', 'label' => 'Off-Road Vehicles'],
                    ['value' => 'IAB2-17', 'label' => 'Performance Vehicles'],
                    ['value' => 'IAB2-18', 'label' => 'Pickup'],
                    ['value' => 'IAB2-19', 'label' => 'Road-Side Assistance'],
                    ['value' => 'IAB2-20', 'label' => 'Sedan'],
                    ['value' => 'IAB2-21', 'label' => 'Trucks & Accessories'],
                    ['value' => 'IAB2-22', 'label' => 'Vintage Cars'],
                    ['value' => 'IAB2-23', 'label' => 'Wagon'],
                ]
            ],

            ['value' => 'IAB3', 'label' => 'Business',
                'children' => [
                    ['value' => 'IAB3-1', 'label' => 'Advertising'],
                    ['value' => 'IAB3-2', 'label' => 'Agriculture'],
                    ['value' => 'IAB3-3', 'label' => 'Biotech/Biomedical'],
                    ['value' => 'IAB3-4', 'label' => 'Business Software'],
                    ['value' => 'IAB3-5', 'label' => 'Construction'],
                    ['value' => 'IAB3-6', 'label' => 'Forestry'],
                    ['value' => 'IAB3-7', 'label' => 'Government'],
                    ['value' => 'IAB3-8', 'label' => 'Green Solutions'],
                    ['value' => 'IAB3-9', 'label' => 'Human Resources'],
                    ['value' => 'IAB3-10', 'label' => 'Logistics'],
                    ['value' => 'IAB3-11', 'label' => 'Marketing'],
                    ['value' => 'IAB3-12', 'label' => 'Metals'],
                ]
            ],

            ['value' => 'IAB4', 'label' => 'Careers',
                'children' => [
                    ['value' => 'IAB4-1', 'label' => 'Career Planning'],
                    ['value' => 'IAB4-2', 'label' => 'College'],
                    ['value' => 'IAB4-3', 'label' => 'Financial Aid'],
                    ['value' => 'IAB4-4', 'label' => 'Job Fairs'],
                    ['value' => 'IAB4-5', 'label' => 'Job Search'],
                    ['value' => 'IAB4-6', 'label' => 'Resume Writing/Advice'],
                    ['value' => 'IAB4-7', 'label' => 'Nursing'],
                    ['value' => 'IAB4-8', 'label' => 'Scholarships'],
                    ['value' => 'IAB4-9', 'label' => 'Telecommuting'],
                    ['value' => 'IAB4-10', 'label' => 'U.S. Military'],
                    ['value' => 'IAB4-11', 'label' => 'Career Advice'],
                ]
            ],

            ['value' => 'IAB5', 'label' => 'Education',
                'children' => [
                    ['value' => 'IAB5-1', 'label' => '7-12 Education'],
                    ['value' => 'IAB5-2', 'label' => 'Adult Education'],
                    ['value' => 'IAB5-3', 'label' => 'Art History'],
                    ['value' => 'IAB5-4', 'label' => 'College Administration'],
                    ['value' => 'IAB5-5', 'label' => 'College Life'],
                    ['value' => 'IAB5-6', 'label' => 'Distance Learning'],
                    ['value' => 'IAB5-7', 'label' => 'English as a 2nd Language'],
                    ['value' => 'IAB5-8', 'label' => 'Language Learning'],
                    ['value' => 'IAB5-9', 'label' => 'Graduate School'],
                    ['value' => 'IAB5-10', 'label' => 'Homeschooling'],
                    ['value' => 'IAB5-11', 'label' => 'Homework/Study Tips'],
                    ['value' => 'IAB5-12', 'label' => 'K-6 Educators'],
                    ['value' => 'IAB5-13', 'label' => 'Private School'],
                    ['value' => 'IAB5-14', 'label' => 'Special Education'],
                    ['value' => 'IAB5-15', 'label' => 'Studying Business'],
                ]
            ],

            ['value' => 'IAB6', 'label' => 'Family & Parenting',
                'children' => [
                    ['value' => 'IAB6-1', 'label' => 'Adoption'],
                    ['value' => 'IAB6-2', 'label' => 'Babies & Toddlers'],
                    ['value' => 'IAB6-3', 'label' => 'Daycare/Pre School'],
                    ['value' => 'IAB6-4', 'label' => 'Family Internet'],
                    ['value' => 'IAB6-5', 'label' => 'Parenting - K-6 Kids'],
                    ['value' => 'IAB6-6', 'label' => 'Parenting teens'],
                    ['value' => 'IAB6-7', 'label' => 'Pregnancy'],
                    ['value' => 'IAB6-8', 'label' => 'Special Needs Kids'],
                    ['value' => 'IAB6-9', 'label' => 'Eldercare'],
                ]
            ],

            ['value' => 'IAB7', 'label' => 'Health & Fitness',
                'children' => [
                    ['value' => 'IAB7-1', 'label' => 'Exercise'],
                    ['value' => 'IAB7-2', 'label' => 'ADD'],
                    ['value' => 'IAB7-3', 'label' => 'AIDS/HIV'],
                    ['value' => 'IAB7-4', 'label' => 'Allergies'],
                    ['value' => 'IAB7-5', 'label' => 'Alternative Medicine'],
                    ['value' => 'IAB7-6', 'label' => 'Arthritis'],
                    ['value' => 'IAB7-7', 'label' => 'Asthma'],
                    ['value' => 'IAB7-8', 'label' => 'Autism/PDD'],
                    ['value' => 'IAB7-9', 'label' => 'Bipolar Disorder'],
                    ['value' => 'IAB7-10', 'label' => 'Brain Tumor'],
                    ['value' => 'IAB7-11', 'label' => 'Cancer'],
                    ['value' => 'IAB7-12', 'label' => 'Cholesterol'],
                    ['value' => 'IAB7-13', 'label' => 'Chronic Fatigue Syndrome'],
                    ['value' => 'IAB7-14', 'label' => 'Chronic Pain'],
                    ['value' => 'IAB7-15', 'label' => 'Cold & Flu'],
                    ['value' => 'IAB7-16', 'label' => 'Deafness'],
                    ['value' => 'IAB7-17', 'label' => 'Dental Care'],
                    ['value' => 'IAB7-18', 'label' => 'Depression'],
                    ['value' => 'IAB7-19', 'label' => 'Dermatology'],
                    ['value' => 'IAB7-20', 'label' => 'Diabetes'],
                    ['value' => 'IAB7-21', 'label' => 'Epilepsy'],
                    ['value' => 'IAB7-22', 'label' => 'GERD/Acid Reflux'],
                    ['value' => 'IAB7-23', 'label' => 'Headaches/Migraines'],
                    ['value' => 'IAB7-24', 'label' => 'Heart Disease'],
                    ['value' => 'IAB7-25', 'label' => 'Herbs for Health'],
                    ['value' => 'IAB7-26', 'label' => 'Holistic Healing'],
                    ['value' => 'IAB7-27', 'label' => 'IBS/Crohn’s Disease'],
                    ['value' => 'IAB7-28', 'label' => 'Incest/Abuse Support'],
                    ['value' => 'IAB7-29', 'label' => 'Incontinence'],
                    ['value' => 'IAB7-30', 'label' => 'Infertility'],
                    ['value' => 'IAB7-31', 'label' => 'Men’s Health'],
                    ['value' => 'IAB7-32', 'label' => 'Nutrition'],
                    ['value' => 'IAB7-33', 'label' => 'Orthopedics'],
                    ['value' => 'IAB7-34', 'label' => 'Panic/Anxiety Disorders'],
                    ['value' => 'IAB7-35', 'label' => 'Pediatrics'],
                    ['value' => 'IAB7-36', 'label' => 'Physical Therapy'],
                    ['value' => 'IAB7-37', 'label' => 'Psychology/Psychiatry'],
                    ['value' => 'IAB7-38', 'label' => 'Senior Health'],
                    ['value' => 'IAB7-39', 'label' => 'Sexuality'],
                    ['value' => 'IAB7-40', 'label' => 'Sleep Disorders'],
                    ['value' => 'IAB7-41', 'label' => 'Smoking Cessation'],
                    ['value' => 'IAB7-42', 'label' => 'Substance Abuse'],
                    ['value' => 'IAB7-43', 'label' => 'Thyroid Disease'],
                    ['value' => 'IAB7-44', 'label' => 'Weight Loss'],
                    ['value' => 'IAB7-45', 'label' => 'Women\'s Health'],
                ]
            ],

            ['value' => 'IAB8', 'label' => 'Food & Drink',
                'children' => [
                    ['value' => 'IAB8-1', 'label' => 'American Cuisine'],
                    ['value' => 'IAB8-2', 'label' => 'Barbecues & Grilling'],
                    ['value' => 'IAB8-3', 'label' => 'Cajun/Creole'],
                    ['value' => 'IAB8-4', 'label' => 'Chinese Cuisine'],
                    ['value' => 'IAB8-5', 'label' => 'Cocktails/Beer'],
                    ['value' => 'IAB8-6', 'label' => 'Coffee/Tea'],
                    ['value' => 'IAB8-7', 'label' => 'Cuisine-Specific'],
                    ['value' => 'IAB8-8', 'label' => 'Desserts & Baking'],
                    ['value' => 'IAB8-9', 'label' => 'Dining Out'],
                    ['value' => 'IAB8-10', 'label' => 'Food Allergies'],
                    ['value' => 'IAB8-11', 'label' => 'French Cuisine'],
                    ['value' => 'IAB8-12', 'label' => 'Health/Low-Fat Cooking'],
                    ['value' => 'IAB8-13', 'label' => 'Italian Cuisine'],
                    ['value' => 'IAB8-14', 'label' => 'Japanese Cuisine'],
                    ['value' => 'IAB8-15', 'label' => 'Mexican Cuisine'],
                    ['value' => 'IAB8-16', 'label' => 'Vegan'],
                    ['value' => 'IAB8-17', 'label' => 'Vegetarian'],
                    ['value' => 'IAB8-18', 'label' => 'Wine'],
                ]
            ],

            ['value' => 'IAB9', 'label' => 'Hobbies & Interests',
                'children' => [
                    ['value' => 'IAB9-1', 'label' => 'Art/Technology'],
                    ['value' => 'IAB9-2', 'label' => 'Arts & Crafts'],
                    ['value' => 'IAB9-3', 'label' => 'Beadwork'],
                    ['value' => 'IAB9-4', 'label' => 'Bird-Watching'],
                    ['value' => 'IAB9-5', 'label' => 'Board Games/Puzzles'],
                    ['value' => 'IAB9-6', 'label' => 'Candle & Soap Making'],
                    ['value' => 'IAB9-7', 'label' => 'Card Games'],
                    ['value' => 'IAB9-8', 'label' => 'Chess'],
                    ['value' => 'IAB9-9', 'label' => 'Cigars'],
                    ['value' => 'IAB9-10', 'label' => 'Collecting'],
                    ['value' => 'IAB9-11', 'label' => 'Comic Books'],
                    ['value' => 'IAB9-12', 'label' => 'Drawing/Sketching'],
                    ['value' => 'IAB9-13', 'label' => 'Freelance Writing'],
                    ['value' => 'IAB9-14', 'label' => 'Genealogy'],
                    ['value' => 'IAB9-15', 'label' => 'Getting Published'],
                    ['value' => 'IAB9-16', 'label' => 'Guitar'],
                    ['value' => 'IAB9-17', 'label' => 'Home Recording'],
                    ['value' => 'IAB9-18', 'label' => 'Investors & Patents'],
                    ['value' => 'IAB9-19', 'label' => 'Jewelry Making'],
                    ['value' => 'IAB9-20', 'label' => 'Magic & Illusion'],
                    ['value' => 'IAB9-21', 'label' => 'Needlework'],
                    ['value' => 'IAB9-22', 'label' => 'Painting'],
                    ['value' => 'IAB9-23', 'label' => 'Photography'],
                    ['value' => 'IAB9-24', 'label' => 'Radio'],
                    ['value' => 'IAB9-25', 'label' => 'Roleplaying Games'],
                    ['value' => 'IAB9-26', 'label' => 'Sci-Fi & Fantasy'],
                    ['value' => 'IAB9-27', 'label' => 'Scrapbooking'],
                    ['value' => 'IAB9-28', 'label' => 'Screenwriting'],
                    ['value' => 'IAB9-29', 'label' => 'Stamps & Coins'],
                    ['value' => 'IAB9-30', 'label' => 'Video & Computer Games'],
                    ['value' => 'IAB9-31', 'label' => 'Woodworking'],

                ]
            ],

            ['value' => 'IAB10', 'label' => 'Home & Garden',
                'children' => [
                    ['value' => 'IAB10-1', 'label' => 'Appliances'],
                    ['value' => 'IAB10-2', 'label' => 'Entertaining'],
                    ['value' => 'IAB10-3', 'label' => 'Environmental Safety'],
                    ['value' => 'IAB10-4', 'label' => 'Gardening'],
                    ['value' => 'IAB10-5', 'label' => 'Home Repair'],
                    ['value' => 'IAB10-6', 'label' => 'Home Theater'],
                    ['value' => 'IAB10-7', 'label' => 'Interior Decorating'],
                    ['value' => 'IAB10-8', 'label' => 'Landscaping'],
                    ['value' => 'IAB10-9', 'label' => 'Remodeling & Construction'],
                ]
            ],

            ['value' => 'IAB11', 'label' => 'Law, Government, & Politics',
                'children' => [
                    ['value' => 'IAB11-1', 'label' => 'Immigration'],
                    ['value' => 'IAB11-2', 'label' => 'Legal Issues'],
                    ['value' => 'IAB11-3', 'label' => 'U.S. Government Resources'],
                    ['value' => 'IAB11-4', 'label' => 'Politics'],
                    ['value' => 'IAB11-5', 'label' => 'Commentary'],
                ]
            ],

            ['value' => 'IAB12', 'label' => 'News',
                'children' => [
                    ['value' => 'IAB12-1', 'label' => 'International News'],
                    ['value' => 'IAB12-2', 'label' => 'National News'],
                    ['value' => 'IAB12-3', 'label' => 'Local News'],
                ]
            ],

            ['value' => 'IAB13', 'label' => 'Personal Finance',
                'children' => [
                    ['value' => 'IAB13-1', 'label' => 'Beginning Investing'],
                    ['value' => 'IAB13-2', 'label' => 'Credit/Debt & Loans'],
                    ['value' => 'IAB13-3', 'label' => 'Financial News'],
                    ['value' => 'IAB13-4', 'label' => 'Financial Planning'],
                    ['value' => 'IAB13-5', 'label' => 'Hedge Fund'],
                    ['value' => 'IAB13-6', 'label' => 'Insurance'],
                    ['value' => 'IAB13-7', 'label' => 'Investing'],
                    ['value' => 'IAB13-8', 'label' => 'Mutual Funds'],
                    ['value' => 'IAB13-9', 'label' => 'Options'],
                    ['value' => 'IAB13-10', 'label' => 'Retirement Planning'],
                    ['value' => 'IAB13-11', 'label' => 'Stocks'],
                    ['value' => 'IAB13-12', 'label' => 'Tax Planning'],
                ]
            ],

            ['value' => 'IAB14', 'label' => 'Society',
                'children' => [
                    ['value' => 'IAB14-1', 'label' => 'Dating'],
                    ['value' => 'IAB14-2', 'label' => 'Divorce Support'],
                    ['value' => 'IAB14-3', 'label' => 'Gay Life'],
                    ['value' => 'IAB14-4', 'label' => 'Marriage'],
                    ['value' => 'IAB14-5', 'label' => 'Senior Living'],
                    ['value' => 'IAB14-6', 'label' => 'Teens'],
                    ['value' => 'IAB14-7', 'label' => 'Weddings'],
                    ['value' => 'IAB14-8', 'label' => 'Ethnic Specific'],
                ]

            ],

            ['value' => 'IAB15', 'label' => 'Science',
                'children' => [
                    ['value' => 'IAB15-1', 'label' => 'Astrology'],
                    ['value' => 'IAB15-2', 'label' => 'Biology'],
                    ['value' => 'IAB15-3', 'label' => 'Chemistry'],
                    ['value' => 'IAB15-4', 'label' => 'Geology'],
                    ['value' => 'IAB15-5', 'label' => 'Paranormal Phenomena'],
                    ['value' => 'IAB15-6', 'label' => 'Physics'],
                    ['value' => 'IAB15-7', 'label' => 'Space/Astronomy'],
                    ['value' => 'IAB15-8', 'label' => 'Geography'],
                    ['value' => 'IAB15-9', 'label' => 'Botany'],
                    ['value' => 'IAB15-10', 'label' => 'Weather'],
                ]
            ],

            ['value' => 'IAB16', 'label' => 'Pets',
                'children' => [
                    ['value' => 'IAB16-1', 'label' => 'Aquariums'],
                    ['value' => 'IAB16-2', 'label' => 'Birds'],
                    ['value' => 'IAB16-3', 'label' => 'Cats'],
                    ['value' => 'IAB16-4', 'label' => 'Dogs'],
                    ['value' => 'IAB16-5', 'label' => 'Large Animals'],
                    ['value' => 'IAB16-6', 'label' => 'Reptiles'],
                    ['value' => 'IAB16-7', 'label' => 'Veterinary Medicine'],
                ]
            ],

            ['value' => 'IAB17', 'label' => 'Sports',
                'children' => [
                    ['value' => 'IAB17-1', 'label' => 'Auto Racing'],
                    ['value' => 'IAB17-2', 'label' => 'Baseball'],
                    ['value' => 'IAB17-3', 'label' => 'Bicycling'],
                    ['value' => 'IAB17-4', 'label' => 'Bodybuilding'],
                    ['value' => 'IAB17-5', 'label' => 'Boxing'],
                    ['value' => 'IAB17-6', 'label' => 'Canoeing/Kayaking'],
                    ['value' => 'IAB17-7', 'label' => 'Cheerleading'],
                    ['value' => 'IAB17-8', 'label' => 'Climbing'],
                    ['value' => 'IAB17-9', 'label' => 'Cricket'],
                    ['value' => 'IAB17-10', 'label' => 'Figure Skating'],
                    ['value' => 'IAB17-11', 'label' => 'Fly Fishing'],
                    ['value' => 'IAB17-12', 'label' => 'Football'],
                    ['value' => 'IAB17-13', 'label' => 'Freshwater Fishing'],
                    ['value' => 'IAB17-14', 'label' => 'Game & Fish'],
                    ['value' => 'IAB17-15', 'label' => 'Golf'],
                    ['value' => 'IAB17-16', 'label' => 'Horse Racing'],
                    ['value' => 'IAB17-17', 'label' => 'Horses'],
                    ['value' => 'IAB17-18', 'label' => 'Hunting/Shooting'],
                    ['value' => 'IAB17-19', 'label' => 'Inline Skating'],
                    ['value' => 'IAB17-20', 'label' => 'Martial Arts'],
                    ['value' => 'IAB17-21', 'label' => 'Mountain Biking'],
                    ['value' => 'IAB17-22', 'label' => 'NASCAR Racing'],
                    ['value' => 'IAB17-23', 'label' => 'Olympics'],
                    ['value' => 'IAB17-24', 'label' => 'Paintball'],
                    ['value' => 'IAB17-25', 'label' => 'Power & Motorcycles'],
                    ['value' => 'IAB17-26', 'label' => 'Pro Basketball'],
                    ['value' => 'IAB17-27', 'label' => 'Pro Ice Hockey'],
                    ['value' => 'IAB17-28', 'label' => 'Rodeo'],
                    ['value' => 'IAB17-29', 'label' => 'Rugby'],
                    ['value' => 'IAB17-30', 'label' => 'Running/Jogging'],
                    ['value' => 'IAB17-31', 'label' => 'Sailing'],
                    ['value' => 'IAB17-32', 'label' => 'Saltwater Fishing'],
                    ['value' => 'IAB17-33', 'label' => 'Scuba Diving'],
                    ['value' => 'IAB17-34', 'label' => 'Skateboarding'],
                    ['value' => 'IAB17-35', 'label' => 'Skiing'],
                    ['value' => 'IAB17-36', 'label' => 'Snowboarding'],
                    ['value' => 'IAB17-37', 'label' => 'Surfing/Body-Boarding'],
                    ['value' => 'IAB17-38', 'label' => 'Swimming'],
                    ['value' => 'IAB17-39', 'label' => 'Table Tennis/Ping-Pong'],
                    ['value' => 'IAB17-40', 'label' => 'Tennis'],
                    ['value' => 'IAB17-41', 'label' => 'Volleyball'],
                    ['value' => 'IAB17-42', 'label' => 'Walking'],
                    ['value' => 'IAB17-43', 'label' => 'Waterski/Wakeboard'],
                    ['value' => 'IAB17-44', 'label' => 'World Soccer'],
                ]
            ],

            ['value' => 'IAB18', 'label' => 'Style & Fashion',
                'children' => [
                    ['value' => 'IAB18-1', 'label' => 'Beauty'],
                    ['value' => 'IAB18-2', 'label' => 'Body Art'],
                    ['value' => 'IAB18-3', 'label' => 'Fashion'],
                    ['value' => 'IAB18-4', 'label' => 'Jewelry'],
                    ['value' => 'IAB18-5', 'label' => 'Clothing'],
                    ['value' => 'IAB18-6', 'label' => 'Accessories'],
                ]
            ],

            ['value' => 'IAB19', 'label' => 'Technology & Computing',
                'children' => [
                    ['value' => 'IAB19-1', 'label' => '3-D Graphics'],
                    ['value' => 'IAB19-2', 'label' => 'Animation'],
                    ['value' => 'IAB19-3', 'label' => 'Antivirus Software'],
                    ['value' => 'IAB19-4', 'label' => 'C/C++'],
                    ['value' => 'IAB19-5', 'label' => 'Cameras & Camcorders'],
                    ['value' => 'IAB19-6', 'label' => 'Cell Phones'],
                    ['value' => 'IAB19-7', 'label' => 'Computer Certification'],
                    ['value' => 'IAB19-8', 'label' => 'Computer Networking'],
                    ['value' => 'IAB19-9', 'label' => 'Computer Peripherals'],
                    ['value' => 'IAB19-10', 'label' => 'Computer Reviews'],
                    ['value' => 'IAB19-11', 'label' => 'Data Centers'],
                    ['value' => 'IAB19-12', 'label' => 'Databases'],
                    ['value' => 'IAB19-13', 'label' => 'Desktop Publishing'],
                    ['value' => 'IAB19-14', 'label' => 'Desktop Video'],
                    ['value' => 'IAB19-15', 'label' => 'Email'],
                    ['value' => 'IAB19-16', 'label' => 'Graphics Software'],
                    ['value' => 'IAB19-17', 'label' => 'Home Video/DVD'],
                    ['value' => 'IAB19-18', 'label' => 'Internet Technology'],
                    ['value' => 'IAB19-19', 'label' => 'Java'],
                    ['value' => 'IAB19-20', 'label' => 'JavaScript'],
                    ['value' => 'IAB19-21', 'label' => 'Mac Support'],
                    ['value' => 'IAB19-22', 'label' => 'MP3/MIDI'],
                    ['value' => 'IAB19-23', 'label' => 'Net Conferencing'],
                    ['value' => 'IAB19-24', 'label' => 'Net for Beginners'],
                    ['value' => 'IAB19-25', 'label' => 'Network Security'],
                    ['value' => 'IAB19-26', 'label' => 'Palmtops/PDAs'],
                    ['value' => 'IAB19-27', 'label' => 'PC Support'],
                    ['value' => 'IAB19-28', 'label' => 'Portable'],
                    ['value' => 'IAB19-29', 'label' => 'Entertainment'],
                    ['value' => 'IAB19-30', 'label' => 'Shareware/Freeware'],
                    ['value' => 'IAB19-31', 'label' => 'Unix'],
                    ['value' => 'IAB19-32', 'label' => 'Visual Basic'],
                    ['value' => 'IAB19-33', 'label' => 'Web Clip Art'],
                    ['value' => 'IAB19-34', 'label' => 'Web Design/HTML'],
                    ['value' => 'IAB19-35', 'label' => 'Web Search'],
                    ['value' => 'IAB19-36', 'label' => 'Windows'],
                ]
            ],

            ['value' => 'IAB20', 'label' => 'Travel',
                'children' => [
                    ['value' => 'IAB20-1', 'label' => 'Adventure Travel'],
                    ['value' => 'IAB20-2', 'label' => 'Africa'],
                    ['value' => 'IAB20-3', 'label' => 'Air Travel'],
                    ['value' => 'IAB20-4', 'label' => 'Australia & New Zealand'],
                    ['value' => 'IAB20-5', 'label' => 'Bed & Breakfasts'],
                    ['value' => 'IAB20-6', 'label' => 'Budget Travel'],
                    ['value' => 'IAB20-7', 'label' => 'Business Travel'],
                    ['value' => 'IAB20-8', 'label' => 'By US Locale'],
                    ['value' => 'IAB20-9', 'label' => 'Camping'],
                    ['value' => 'IAB20-10', 'label' => 'Canada'],
                    ['value' => 'IAB20-11', 'label' => 'Caribbean'],
                    ['value' => 'IAB20-12', 'label' => 'Cruises'],
                    ['value' => 'IAB20-13', 'label' => 'Eastern Europe'],
                    ['value' => 'IAB20-14', 'label' => 'Europe'],
                    ['value' => 'IAB20-15', 'label' => 'France'],
                    ['value' => 'IAB20-16', 'label' => 'Greece'],
                    ['value' => 'IAB20-17', 'label' => 'Honeymoons/Getaways'],
                    ['value' => 'IAB20-18', 'label' => 'Hotels'],
                    ['value' => 'IAB20-19', 'label' => 'Italy'],
                    ['value' => 'IAB20-20', 'label' => 'Japan'],
                    ['value' => 'IAB20-21', 'label' => 'Mexico & Central America'],
                    ['value' => 'IAB20-22', 'label' => 'National Parks'],
                    ['value' => 'IAB20-23', 'label' => 'South America'],
                    ['value' => 'IAB20-24', 'label' => 'Spas'],
                    ['value' => 'IAB20-25', 'label' => 'Theme Parks'],
                    ['value' => 'IAB20-26', 'label' => 'Traveling with Kids'],
                    ['value' => 'IAB20-27', 'label' => 'United Kingdom'],
                ]
            ],

            ['value' => 'IAB21', 'label' => 'Real Estate',
                'children' => [
                    ['value' => 'IAB21-1', 'label' => 'Apartments'],
                    ['value' => 'IAB21-2', 'label' => 'Architects'],
                    ['value' => 'IAB21-3', 'label' => 'Buying/Selling Homes'],
                ]
            ],

            ['value' => 'IAB22', 'label' => 'Shopping',
                'children' => [
                    ['value' => 'IAB22-1', 'label' => 'Contests & Freebies'],
                    ['value' => 'IAB22-2', 'label' => 'Couponing'],
                    ['value' => 'IAB22-3', 'label' => 'Comparison'],
                    ['value' => 'IAB22-4', 'label' => 'Engines'],
                ]
            ],

            ['value' => 'IAB23', 'label' => 'Religion & Spirituality',
                'children' => [
                    ['value' => 'IAB23-1', 'label' => 'Alternative Religions'],
                    ['value' => 'IAB23-2', 'label' => 'Atheism/Agnosticism'],
                    ['value' => 'IAB23-3', 'label' => 'Buddhism'],
                    ['value' => 'IAB23-4', 'label' => 'Catholicism'],
                    ['value' => 'IAB23-5', 'label' => 'Christianity'],
                    ['value' => 'IAB23-6', 'label' => 'Hinduism'],
                    ['value' => 'IAB23-7', 'label' => 'Islam'],
                    ['value' => 'IAB23-8', 'label' => 'Judaism'],
                    ['value' => 'IAB23-9', 'label' => 'Latter-Day Saints'],
                    ['value' => 'IAB23-10', 'label' => 'Pagan/Wiccan'],
                ]
            ],

            ['value' => 'IAB24', 'label' => 'Uncategorized',
                'children' => [
                    ['value' => 'IAB24', 'label' => 'Uncategorized']
                ]
            ],
            [
                'value' => 'IAB25', 'label' => 'Non-Standard Content',
                'children' => [
                    ['value' => 'IAB25-1', 'label' => 'Unmoderated UGC'],
                    ['value' => 'IAB25-2', 'label' => 'Extreme Graphic/Explicit Violence'],
                    ['value' => 'IAB25-3', 'label' => 'Pornography'],
                    ['value' => 'IAB25-4', 'label' => 'Profane Content'],
                    ['value' => 'IAB25-5', 'label' => 'Hate Content'],
                    ['value' => 'IAB25-6', 'label' => 'Under Construction'],
                    ['value' => 'IAB25-7', 'label' => 'Incentivized'],
                ]
            ],
            [
                'value' => 'IAB26',
                'label' => 'Illegal Content',
                'children' => [
                    ['value' => 'IAB26-1', 'label' => 'Illegal Content'],
                    ['value' => 'IAB26-2', 'label' => 'Warez'],
                    ['value' => 'IAB26-3', 'label' => 'Spyware/Malware'],
                    ['value' => 'IAB26-4', 'label' => 'Copyright Infringement'],
                ]

            ]
        ];

        return $this->jsonResponse($data);
    }
}
