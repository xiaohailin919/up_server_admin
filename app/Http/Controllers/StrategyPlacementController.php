<?php

namespace App\Http\Controllers;

use App\Helpers\TimeConversion;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\MySql\Placement;
use App\Models\MySql\StrategyPlacement;
use App\Models\MySql\Placement as PlacementMyModel;

use App\Events\UpdatePlacement;
use App\Helpers\Utils;

use Grpc;
use Proto\SqsQueueParam;
use Proto\RpcWaterFallServiceClient;

class StrategyPlacementController extends ApiController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = StrategyPlacement::query()
            ->from(StrategyPlacement::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->select([
                't1.id', 't1.placement_id', 't2.uuid as placement_uuid', 't2.name as placement_name', 't2.format',
                't1.status', 't1.create_time', 't1.update_time'
            ])
            ->orderByDesc('id');

        if ($request->has('placement_id')) {
            $value = $request->query('placement_id');
            $query->where(static function($subQuery) use ($value) {
                $subQuery->where('t2.id', $value)->orWhere('t2.uuid', $value);
            });
        }
        if (array_key_exists($request->query('format', -1), StrategyPlacement::getFormatMap())) {
            $query->where('t2.format', $request->query('format'));
        }
        if (array_key_exists($request->query('status', -1), StrategyPlacement::getStatusMap(true))) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        return $this->jsonResponse($data);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $strategy = StrategyPlacement::query()->where('id', $id)->firstOrFail();

        $strategy['pacing'] = TimeConversion::secondToMinute($strategy['pacing']);

        $strategy['format'] = Placement::query()->where('id', $strategy['placement_id'])->value('format');

        return $this->jsonResponse($strategy);
    }


    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $strategy = StrategyPlacement::query()->where('id', $id)->firstOrFail();
        $format = Placement::query()->where('id', $strategy['placement_id'])->value('format');

        /**
         * strategy_cache_time       Cache Time
         * strategy_cache_timeout    等待策略更新超时时间
         * wifi_autoplay             WIFI 视频自动播放
         * nw_requests               Network 并发请求数
         * nw_cache_time             Network 缓存时间
         * nw_timeout                Network 广告素材超时时间
         * ad_data_nw_timeout        Network 广告数据超时时间
         * auto_refresh              Auto Refresh
         * auto_refresh_time         Auto Refresh Time
         * status                    Status
         * sdk_timeout               长超时时间
         * load_success_up_status    Placement 维度 UpStatus 有效期
         * ad_up_status              Ad Source 维度 UpStatus 有效期
         * load_fail_wtime           Load 失败后重试最小等待时间
         * load_cap                  广告位 Load Cap 设置
         * load_cap_time             广告位 Load Cap 计时周期
         * cached_offers_num         有效缓存数量设置
         * bid_token_cache_time      Bid Token 缓存有效期
         * header_bidding_timeout    Header Bidding 有效时间
         * hb_start_time             WaterFall HB 最大超时时间
         * hb_bid_timeout            Bid 询价最大等待时间
         * bid_fail_interval         Bid 失败后下次 Bid 时间间隔
         * request_auto              自动 Request
         * extra_template            Native Template
         * extra_parameter           Native Splash Template extra Parameter
         * my_offer_num              myOffer 请求条数
         * use_my_offer_no_filled    是否使用兜底 MyOffer
         * preload_my_offer          是否预加载 MyOffer
         * click_address             点击 Tracking 服务器地址
         * opensource_click_address  开源版点击 Tracking 服务器地址
         * click_address_timeout_min 点击 Tracking 延时上报规则
         * click_address_timeout_max 点击 Tracking 延时上报规则
         * sync_ilrd_2_mmp           广告展示收益同步给三方监测平台
         * fbhb_bid_wtime            Facebook HB广告源询价最大等待时间
         */
        $input = $request->input();

        $data = [
            'status'               => $request->input('status', 1),
            'myoffer_num'          => $request->input('myoffer_num', 3),
            'use_myoffer_nofilled' => $request->input('use_myoffer_nofilled', 0),
            'preload_myoffer'      => $request->input('preload_myoffer', 2),
            'update_time'          => time(),
        ];

//        if(isset($input['show_type'])){
//            $data['show_type'] = $input['show_type'];
//        }
        if(isset($input['wifi_autoplay'])){
            $data['wifi_autoplay'] = $input['wifi_autoplay'];
        }
        if(isset($input['nw_cache_time'])){
            $data['nw_cache_time'] = $input['nw_cache_time'];
        }
        if(isset($input['nw_timeout'])){
            $data['nw_timeout'] = $input['nw_timeout'];
        }
        if(isset($input['ad_data_nw_timeout'])){
            $data['ad_data_nw_timeout'] = $input['ad_data_nw_timeout'];
        }
        if(isset($input['strategy_cache_time'])){
            $data['strategy_cache_time'] = $input['strategy_cache_time'];
        }
//        dev20200612 该配置项已废弃
//        if(isset($input['show_sync'])){
//            $data['show_sync'] = $input['show_sync'];
//        }
//        if(isset($input['click_sync'])){
//            $data['click_sync'] = $input['click_sync'];
//        }
        if(isset($input['strategy_cache_timeout']) && $input['strategy_cache_timeout'] != ""){
            Log::info("get_post:" . $input['strategy_cache_timeout']);
            $data['strategy_cache_timeout'] = $input['strategy_cache_timeout'];
        } else{
            $cacheTimeout = $this->getCacheTimeoutByFormat($format);
            Log::info("get_default:" . $cacheTimeout);
            $data['strategy_cache_timeout'] = $cacheTimeout;
        }
        if(isset($input['nw_requests'])){
            $data['nw_requests'] = $input['nw_requests'];
        }
        if(isset($input['nw_offer_requests'])){
            $data['nw_offer_requests'] = $input['nw_offer_requests'];
        }
        if(isset($input['refresh'])){
            $data['refresh'] = $input['refresh'];
        }
        if(isset($input['cap_hour'])){
            $data['cap_hour'] = $input['cap_hour'];
        }
        if(isset($input['cap_day'])){
            $data['cap_day'] = $input['cap_day'];
        }
        if(isset($input['sdk_timeout'])){
            $data['sdk_timeout'] = $input['sdk_timeout'];
        }
        if(isset($input['load_success_up_status'])){
            $data['load_success_up_status'] = $input['load_success_up_status'];
        }
        if(isset($input['load_fail_wtime'])){
            $data['load_fail_wtime'] = $input['load_fail_wtime'];
        }
        if(isset($input['load_cap'])){
            $data['load_cap'] = $input['load_cap'];
        }
        if(isset($input['load_cap_time'])){
            $data['load_cap_time'] = $input['load_cap_time'];
        }
        if(isset($input['cached_offers_num'])){
            $data['cached_offers_num'] = $input['cached_offers_num'];
        }
        if(isset($input['ad_up_status'])){
            $data['ad_up_status'] = $input['ad_up_status'];
        }
        if(isset($input['bid_token_cache_time'])){
            $data['bid_token_cache_time'] = $input['bid_token_cache_time'];
        }
        //header-bidding
        if(isset($input['header_bidding_timeout'])){
            $data['header_bidding_timeout'] = $input['header_bidding_timeout'];
        }
        if (isset($input['hb_start_time'])) {
            $data['hb_start_time'] = $input['hb_start_time'];
        }
        if (isset($input['fbhb_bid_wtime'])) {
            $data['fbhb_bid_wtime'] = $input['fbhb_bid_wtime'];
        }
        if (isset($input['hb_bid_timeout'])) {
            $data['hb_bid_timeout'] = $input['hb_bid_timeout'];
        }
        if (isset($input['bid_fail_interval'])) {
            $data['bid_fail_interval'] = $input['bid_fail_interval'];
        }
        if(isset($input['request_auto'])){
            $data['request_auto'] = $input['request_auto'];
        }
        $data['auto_refresh'] = $input['auto_refresh'] ?? 0;
        if(isset($input['auto_refresh_time'])){
            $data['auto_refresh_time'] = $input['auto_refresh_time'];
        }
        if(isset($input['extra_template'])){
            $data['extra_template'] = $input['extra_template'];
        }
        if(isset($input['extra_parameter'])){
            if (is_numeric($input['extra_template'])) {
                $inputArr = json_decode($input['extra_parameter'],true);
                $lastArr['pucs'] = isset($inputArr['pucs']) && in_array($inputArr['pucs'],[0,1],true) ? intval($inputArr['pucs']) : 1;
                $lastArr['apdt'] = isset($inputArr['apdt']) ? doubleval($inputArr['apdt']) : 1.5;
                $lastArr['aprn'] = isset($inputArr['aprn']) ? intval($inputArr['aprn']) : 6;
                $lastArr['puas'] = isset($inputArr['puas']) && in_array($inputArr['puas'],[0,1],true) ? intval($inputArr['puas']) : 0;
                $lastArr['cdt']  = isset($inputArr['cdt']) ? doubleval($inputArr['cdt']) : 5;
                $lastArr['ski_swt'] = isset($inputArr['ski_swt']) && in_array($inputArr['ski_swt'],[0,1],true) ? intval($inputArr['ski_swt']) : 1;
                $lastArr['aut_swt'] = isset($inputArr['aut_swt']) && in_array($inputArr['aut_swt'],[0,1],true) ? intval($inputArr['aut_swt']) : 1;
                $tmpData = json_encode($lastArr);
            }else{
                $tmpData = $input['extra_parameter'];
            }
            $data['extra_parameter'] = $tmpData;
        }
        if(isset($input['click_address'])){
            $data['click_address'] = $input['click_address'];
        }
        if(isset($input['click_address_timeout_min'])){
            $data['click_address_timeout_min'] = $input['click_address_timeout_min'];
        }
        if(isset($input['click_address_timeout_max'])){
            $data['click_address_timeout_max'] = $input['click_address_timeout_max'];
        }
        if(isset($input['opensource_click_address'])){
            $data['opensource_click_address'] = $input['opensource_click_address'];
        }

        if(isset($input['sync_ilrd_2_mmp'])){
            $data['sync_ilrd_2_mmp'] = Utils::isJson($input['sync_ilrd_2_mmp']) ? $input['sync_ilrd_2_mmp'] : '';
        }

        $strategy->update($data);

        if ($data) {
            $placementId = StrategyPlacement::query()->where('id', $id)->value('placement_id');

            //处理添加队列
            $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
                'credentials' => Grpc\ChannelCredentials::createInsecure()
            ]);
            $args = new SqsQueueParam();
            $args->setType(1);
            $args->setId((int)$placementId);
            $res = $client->PlacementSqsQueue($args)->wait();
            if ($res[1]->code !== 0) {
                Log::info("update placement quque failed:" . $res[1]->details);
            } else {
                Log::info("update placement quque succ");
            }

            //触发更新placement的事件
            Event::fire(new UpdatePlacement($placementId));
        }

        return $this->jsonResponse();
    }

    private function getCacheTimeoutByFormat($format): int
    {
        /*
         Ad type=Splash   默认0秒
         Ad type=RV、interstitial、Banner，Native 默认2秒
         */
        $cacheTimeOut = 0; // splash
        switch($format){
            case PlacementMyModel::FORMAT_RV:
            case PlacementMyModel::FORMAT_INTERSTITIAL:
            case PlacementMyModel::FORMAT_BANNER:
            case PlacementMyModel::FORMAT_NATIVE:
                $cacheTimeOut = 2;
                break;
        }

        return $cacheTimeOut;
    }
}
