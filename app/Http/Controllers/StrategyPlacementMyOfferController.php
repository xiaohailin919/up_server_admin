<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\Base;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\StrategyPlacementMyOffer;
use App\Models\MySql\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Proto\RpcWaterFallServiceClient;
use Grpc;
use Proto\SqsQueueParam;
use Illuminate\Support\Facades\Log;

class StrategyPlacementMyOfferController extends ApiController
{

    public function index(Request $request): JsonResponse
    {
        $query = StrategyPlacementMyOffer::query()
            ->from(StrategyPlacementMyOffer::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->leftJoin(App::TABLE . ' as t3', 't3.id', '=', 't2.app_id')
            ->leftJoin(Publisher::TABLE . ' as t4', 't4.id', '=', 't2.publisher_id')
            ->leftJoin(Users::TABLE . ' as t5', 't5.id', '=', 't1.admin_id')
            ->select([
                't1.id', 't1.placement_id', 't2.uuid as placement_uuid', 't2.name as placement_name',
                't3.uuid as app_uuid', 't3.name as app_name', 't4.id as publisher_id', 't4.name as publisher_name',
                't1.format', 't5.name as admin_name', 't1.update_time', 't1.status'
            ])
            ->orderByDesc('t1.id');

        if ($request->has('placement_id')) {
            $tmp = $request->query('placement_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t1.placement_id', $tmp)->orWhere('t2.uuid', $tmp);
            })->where('t1.placement_id', '!=', 0);
        }
        if ($request->has('placement_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('placement_name') . '%');
        }
        if ($request->has('app_id')) {
            $tmp = $request->query('app_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            })->where('t1.placement_id', '!=', 0);
        }
        if ($request->has('publisher_id')) {
            $query->where('t2.publisher_id', $request->query('publisher_id'));
        }
        if (array_key_exists($request->query('format', -1), Base::getFormatMap())) {
            $query->where('t1.format', $request->query('format'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {
            if ($datum['placement_id'] == 0) {
                $data['list'][$idx]['placement_uuid'] = 0;
                $data['list'][$idx]['placement_name'] = Base::getFormatName($datum['format']) . ' default';
                $data['list'][$idx]['app_uuid']       = 0;
                $data['list'][$idx]['app_name']       = '-';
                $data['list'][$idx]['publisher_id']   = 0;
                $data['list'][$idx]['publisher_name'] = '-';
            }
            unset($data['list'][$idx]['placement_id']);
        }

        return $this->jsonResponse($data);
    }

    public function store(Request $request)
    {
        $rules = [
            'placement_id_list'    => ['array', 'required'],
            'placement_id_list.*'  => ['exists:placement,uuid', 'distinct'],
            'material_timeout'     => ['required'],
            'video_clickable'      => ['required'],
            'show_banner_time'     => ['required'],
            'endcard_click_area'   => ['required'],
            'video_mute'           => ['required'],
            'show_close_time'      => ['required'],
            'offer_cache_time'     => ['required'],
            'apk_download_confirm' => ['required'],
            'storekit_time'        => ['required'],
            'status'               => ['required'],
        ];

        $this->validate($request, $rules);

        $placementIdList = array_column(Placement::query()->whereIn('uuid', $request->get('placement_id_list'))->get(['id'])->toArray(), 'id');

        if (StrategyPlacementMyOffer::query()->whereIn('placement_id', $placementIdList)->exists()) {
            return $this->jsonResponse(['placement_id' => 'Same placement id record has already existed!'], 10000);
        }

        /* 获取所有 Placement 的 id => format 映射表 */
        $placementIdFormatMap = array_column(
            Placement::query()->whereIn('id', $placementIdList)->get(['id', 'format'])->toArray(), 'format', 'id'
        );

        $data = [
            'material_timeout'     => $request->input('material_timeout'),
            'video_clickable'      => $request->input('video_clickable'),
            'show_banner_time'     => $request->input('show_banner_time'),
            'endcard_click_area'   => $request->input('endcard_click_area'),
            'video_mute'           => $request->input('video_mute'),
            'show_close_time'      => $request->input('show_close_time'),
            'offer_cache_time'     => $request->input('offer_cache_time'),
            'apk_download_confirm' => $request->input('apk_download_confirm'),
            'storekit_time'        => $request->input('storekit_time'),
            'status'               => $request->input('status'),
            'admin_id' => Auth::id(),
            'update_time' => date('Y-m-d H:i:s')
        ];

        $insertions = [];
        /* 如果厂商列表中有传 0，插入该厂商这两种样式的策略 */
        if (in_array(0, $placementIdList, false)) {
            if (StrategyPlacementMyOffer::query()
                ->where('placement_id', 0)
                ->whereIn('format', [Base::FORMAT_RV, Base::FORMAT_INTERSTITIAL])->exists()) {
                return $this->jsonResponse(['format' => 'Default strategy for all placement with RV AND IV has already been set'], 10000);
            }
            $data['placement_id'] = 0;
            $data['format'] = Placement::FORMAT_INTERSTITIAL;
            $data['video_clickable'] = StrategyPlacementMyOffer::VIDEO_CLICKABLE_YES;
            $data['material_timeout'] = 20;
            $data['show_banner_time'] = 0;
            $insertions[] = $data;

            $data['format'] = Placement::FORMAT_INTERSTITIAL;
            $data['show_banner_time'] = 5;
            $insertions[] = $data;
        }

        /* 遍历插入 */
        foreach ($placementIdList as $placementId) {
            if ($placementId == 0) {
                continue;
            }
            $data['placement_id'] = $placementId;
            $data['format'] = $placementIdFormatMap[$placementId];
            $insertions[] = $data;
        }

        try {
            DB::beginTransaction();
            StrategyPlacementMyOffer::query()->insert($insertions);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }

        /* 将所有动过的 Placement 添加进处理队列 */
        foreach ($placementIdList as $placementId) {
            $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
                'credentials' => Grpc\ChannelCredentials::createInsecure()
            ]);
            $args = new SqsQueueParam();
            $args->setType(1);
            $args->setId((int)$placementId);
            $client->PlacementSqsQueue($args)->wait();
        }

        return $this->jsonResponse();

        $notFindPlacementIdList = $existPlacementIdList = [];

       if($notFindPlacement || $itemExist){
           if(!empty($notFindPlacementIdList)){
               $msgList[] = "placementId 未找到:" . implode(", ", $notFindPlacementIdList);
           }

           if(!empty($existPlacementIdList)) {
               $msgList[] = "placementId 已存在:" . implode(", ", $existPlacementIdList);
           }
       }

       if(!empty($msgList)){
           throw ValidationException::withMessages([
               'placement_id' => $msgList,
           ]);
       }

        $dataList = [];
        DB::beginTransaction();
        foreach ($placementIdList as $placementId) {
            $data = [
                'material_timeout' => $request->input("material_timeout", 0),
                'video_clickable' => $request->input("video_clickable_switch", StrategyPlacementMyOffer::VIDEO_CLICKABLE_NO),
                'show_banner_time' => $request->input("show_banner_time", 0),
                'endcard_click_area' => $request->input("end_card_click_area", StrategyPlacementMyOffer::END_CARD_CLICK_AREA_FULL_SCREEN),
                'video_mute' => $request->input("video_mute_switch", StrategyPlacementMyOffer::VIDEO_MUTE_YES),
                'show_close_time' => $request->input("show_close_time", 0),
                'offer_cache_time' => $request->input("offer_cache_time", StrategyPlacementMyOffer::OFFER_CACHE_TIME),
                'status' => $request->input("status", StrategyPlacementMyOffer::STATUS_RUNNING),
                'apk_download_confirm' => $request->input('apk_download_confirm', 1),
                'storekit_time' => $request->input('storekit_time', StrategyPlacementMyOffer::STORE_KIT_TIME_EARLY_LOADING),
                'admin_id' => Auth::id(),
                'update_time' => date('Y-m-d H:i:s')
            ];

            if ($placementId == "0") {
                $data['placement_id'] = 0;
                $data['format'] = Placement::FORMAT_INTERSTITIAL;
                $data['video_clickable'] = StrategyPlacementMyOffer::VIDEO_CLICKABLE_YES;
                $data['material_timeout'] = 20;
                $data['show_banner_time'] = 0;
                $dataList[] = $data;

                $data['placement_id'] = 0;
                $data['format'] = Placement::FORMAT_RV;
                $data['video_clickable'] = StrategyPlacementMyOffer::VIDEO_CLICKABLE_YES;
                $data['material_timeout'] = 20;
                $data['show_banner_time'] = 5;
                $dataList[] = $data;
            } else {
                $onePlacement = $placementModel->getOne(['id', 'format'], ['uuid' => $placementId]);
                $data['placement_id'] = $onePlacement['id'];
                $data['format'] = $onePlacement['format'];
                $dataList[] = $data;

                //处理添加队列
                $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
                    'credentials' => Grpc\ChannelCredentials::createInsecure()
                ]);
                $args = new SqsQueueParam();
                $args->setType(1);
                $args->setId(intval($onePlacement['id']));
                $res = $client->PlacementSqsQueue($args)->wait();
                if ($res[1]->code !== 0) {
                    Log::info("update placement quque failed:" . $res[1]->details);
                } else {
                    Log::info("update placement quque succ");
                }

            }
        }

        if (StrategyPlacementMyOffer::insert($dataList)) {
            DB::commit();
            return redirect("strategy-placement-my-offer");
        }

        DB::rollBack();
        exit("create strategy fail");
    }

    public function show($id): JsonResponse
    {
        $strategy = StrategyPlacementMyOffer::query()->where('id', $id)->firstOrFail();
        if ($strategy['placement_id'] != 0) {
            $strategy['placement_id'] = Placement::query()->where('id', $strategy['placement_id'])->value('uuid');
        }
        return $this->jsonResponse($strategy);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $strategy = StrategyPlacementMyOffer::query()->where('id', $id)->firstOrFail();

        $updateData = [
            'material_timeout'     => $request->input('material_timeout',     $strategy['material_timeout']),
            'video_clickable'      => $request->input('video_clickable',      $strategy['video_clickable']),
            'show_banner_time'     => $request->input('show_banner_time',     $strategy['show_banner_time']),
            'endcard_click_area'   => $request->input('endcard_click_area',   $strategy['endcard_click_area']),
            'video_mute'           => $request->input('video_mute',           $strategy['video_mute']),
            'show_close_time'      => $request->input('show_close_time',      $strategy['show_close_time']),
            'offer_cache_time'     => $request->input('offer_cache_time',     $strategy['offer_cache_time']),
            'apk_download_confirm' => $request->input('apk_download_confirm', $strategy['apk_download_confirm']),
            'storekit_time'        => $request->input('storekit_time',        $strategy['storekit_time']),
            'status'               => $request->input('status',               $strategy['status']),
            'admin_id'             => Auth::id(),
            'update_time'          => date('Y-m-d H:i:s')
        ];

        $strategy->update($updateData);

        if ($strategy['placement_id'] != 0) {
            $client = new RpcWaterFallServiceClient(env("WATERFALL_HOST"),[
                'credentials' => Grpc\ChannelCredentials::createInsecure()
            ]);
            $args = new SqsQueueParam();
            $args->setType(1);
            $args->setId((int)$strategy['placement_id']);
            $res = $client->PlacementSqsQueue($args)->wait();
            if ($res[1]->code !== 0) {
                Log::info("update placement quque failed:" . $res[1]->details);
            } else {
                Log::info("update placement quque succ");
            }
        }

        return $this->jsonResponse();
    }
}
