<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use App\Models\MySql\Users;
use App\Models\MySql\Publisher;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use App\Models\MySql\AdxStrategy;

use App\Helpers\InputFilter;
use App\Helpers\TimeConversion;

class AdxStrategyController extends ApiController
{
    /**
     * ADX策略 列表
     * http://yapi.toponad.com/project/18/interface/api/725
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $placementId   = $request->get('placement_id', '');
        $placementName = $request->get('placement_name', '');
        $appId         = $request->get('app_id', '');
        $publisherId   = $request->get('publisher_id', '');
        $format        = $request->get('format', -1);
        $pageSize      = $request->get('page_size', 10);
        $pageNo        = $request->get('page_no', 1);

        $query = AdxStrategy::query()
            ->from(AdxStrategy::TABLE . ' as st')
            ->leftJoin(Placement::TABLE . ' as p', 'p.id', '=', 'st.placement_id')
            ->leftJoin(App::TABLE . ' as a', 'a.id', '=', 'p.app_id')
            ->leftJoin(Publisher::TABLE . ' as pub', 'pub.id', '=', 'a.publisher_id');

        if($placementId){
            $query->where('p.uuid', $placementId);
        }
        if($placementName){
            $query->where('p.name', 'like', "%{$placementName}%");
        }
        if($appId){
            $query->where('a.uuid', $appId);
        }
        if($publisherId){
            $query->where('pub.id', $publisherId);
        }
        if(in_array($format, array_keys(AdxStrategy::getFormatMap()))){
            $query->where('st.format', $format);
        }

        $selectList = ['st.*',
            'p.uuid as placement_id', 'p.name as placement_name',
            'a.uuid as app_id', 'a.name as app_name',
            'pub.id as publisher_id', 'pub.name as publisher_name'];
        $paginator  = $query->orderBy('st.update_time', 'desc')
            ->paginate($pageSize, $selectList, 'page_no', $pageNo);
        $data       = $this->parseResByPaginator($paginator);

        foreach($data['list'] as &$val){
            $val['publisher_id']   = (string)$val['publisher_id'];
            $val['publisher_name'] = (string)$val['publisher_name'];
            $val['placement_id']   = (string)$val['placement_id'];
            $val['placement_name'] = (string)$val['placement_name'];
            $val['app_id']         = (string)$val['app_id'];
            $val['app_name']       = (string)$val['app_name'];
            $val['admin_name']     = (new Users())->getName($val['admin_id']);
            $val['create_time']    = TimeConversion::dateTimeToMsTimestamp($val['create_time']);
            $val['update_time']    = TimeConversion::dateTimeToMsTimestamp($val['update_time']);
        }

        return $this->jsonResponse($data);
    }

    /**
     * ADX策略 新增
     * http://yapi.toponad.com/project/18/interface/api/743
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $placementIds = (array)$request->input('placement_id_list', []);
        $placementIds = InputFilter::getPlacementIdsByUuids($placementIds);
        $request->merge([
            'placement_id_list' => $placementIds,
        ]);

        $rules = [
            'dimension'            => ['required', 'integer'],
            'placement_id_list'    => ['required_if:dimension,' . AdxStrategy::DIMENSION_PLACEMENT, 'array'],
            'format'               => ['required_if:dimension,' . AdxStrategy::DIMENSION_FORMAT, 'integer', Rule::in(array_keys(AdxStrategy::getFormatMap()))],
            'material_timeout'     => ['required', 'integer'],
            'show_banner_time'     => ['required', 'integer'],
            'video_clickable'      => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getVideoClickableMap()))],
            'endcard_click_area'   => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getEndCardClickAreaMap()))],
            'click_mode'           => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getClickModeMap()))],
            'load_type'            => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getLoadTypeMap()))],
            'impression_ua'        => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getImpressionUaMap()))],
            'click_ua'             => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getClickUaMap()))],
            'apk_download_confirm' => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getApkDownloadConfirmMap()))],
            'storekit_time'        => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getStoreKitTimeMap()))],
            'last_offerids_num'    => ['required', 'integer'],
            'dp_cm'                => ['required', 'integer', RUle::in(array_keys(AdxStrategy::getDpCmMap()))],
            'status'               => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getStatusMap()))],
        ];
        Validator::make($request->all(), $rules)->validate();

        $strategy = $request->only([
            'name'                ,
            'dimension'           ,
            'format'              ,
            'material_timeout'    ,
            'show_banner_time'    ,
            'video_clickable'     ,
            'endcard_click_area'  ,
            'click_mode'          ,
            'load_type'           ,
            'impression_ua'       ,
            'click_ua'            ,
            'apk_download_confirm',
            'storekit_time'       ,
            'last_offerids_num'   ,
            'dp_cm'               ,
            'status'
        ]);

        $response = [];
        if($strategy['dimension'] == AdxStrategy::DIMENSION_FORMAT){
            // Format
            $strategy['placement_id'] = 0;
            $strategy['admin_id']     = Auth::id();
            // 不存在则创建
            $exists = AdxStrategy::query()
                ->where('placement_id', 0)
                ->where('format', $strategy['format'])
                ->exists();
            if(!$exists){
                AdxStrategy::query()->create($strategy);
            }else{
                $response['format'] = '广告类型已存在';
            }
        }else if($strategy['dimension'] == AdxStrategy::DIMENSION_PLACEMENT){
            // Placement
            $placements = Placement::query()
                ->whereIn('id', $placementIds)
                ->get();
            $existsPlacementIds = [];
            foreach($placements as $placement){
                $strategy['placement_id'] = $placement['id'];
                $strategy['format']       = $placement['format'];
                $strategy['admin_id']     = Auth::id();
                // 不存在则创建
                $exists = AdxStrategy::query()
                    ->where('placement_id', $placement['id'])
                    ->first();
                if(!$exists){
                    AdxStrategy::query()->create($strategy);
                }else{
                    $existsPlacementIds[] = (new Placement())->getUuid($exists['placement_id']);
                }
            }
            if(!empty($existsPlacementIds)){
                $response['placement_id_list'] = '以下广告位策略已存在：' . implode('，', $existsPlacementIds);
            }
        }

        if(!empty($response)){
            return $this->jsonResponse($response, 10000, '', 422);
        }
        return $this->jsonResponse();
    }

    /**
     * ADX策略 单条记录
     * http://yapi.toponad.com/project/18/interface/api/734
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = AdxStrategy::query()
            ->where('id', $id)
            ->first();

        $data->create_time = TimeConversion::dateTimeToMsTimestamp($data->create_time);
        $data->update_time = TimeConversion::dateTimeToMsTimestamp($data->update_time);

        return $this->jsonResponse($data);
    }

    /**
     * ADX策略 编辑
     * http://yapi.toponad.com/project/18/interface/api/752
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $strategy = AdxStrategy::query()
            ->where('id', $id)
            ->first();

        $rules = [
            'material_timeout'     => ['required', 'integer'],
            'show_banner_time'     => ['required', 'integer'],
            'video_clickable'      => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getVideoClickableMap()))],
            'endcard_click_area'   => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getEndCardClickAreaMap()))],
            'click_mode'           => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getClickModeMap()))],
            'load_type'            => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getLoadTypeMap()))],
            'impression_ua'        => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getImpressionUaMap()))],
            'click_ua'             => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getClickUaMap()))],
            'apk_download_confirm' => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getApkDownloadConfirmMap()))],
            'storekit_time'        => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getStoreKitTimeMap()))],
            'last_offerids_num'    => ['required', 'integer'],
            'dp_cm'                => ['required', 'integer', RUle::in(array_keys(AdxStrategy::getDpCmMap()))],
            'status'               => ['required', 'integer', Rule::in(array_keys(AdxStrategy::getStatusMap()))],
        ];
        Validator::make($request->all(), $rules)->validate();

        $strategy->fill($request->only([
            'material_timeout'    ,
            'show_banner_time'    ,
            'video_clickable'     ,
            'endcard_click_area'  ,
            'click_mode'          ,
            'load_type'           ,
            'impression_ua'       ,
            'click_ua'            ,
            'apk_download_confirm',
            'storekit_time'       ,
            'last_offerids_num'   ,
            'dp_cm'               ,
            'status'              ,
        ]));
        $strategy->update_time = date('Y-m-d H:i:s');
        $strategy->admin_id    = Auth::id();

        $strategy->update();

        return $this->jsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
