<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Placement;
use App\Models\MySql\StrategyPlacement;
use App\Models\MySql\StrategyPlacementFirm;
use App\Rules\NotExists;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use App\Events\UpdatePlacement;
use Proto\RpcWaterFallServiceClient;
use Grpc;
use Proto\SqsQueueParam;
use Illuminate\Support\Facades\Log;

class StrategyPlacementFirmController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $strategyPlacementId = $request->query('str_pl_id', -1);

        $paginator = StrategyPlacementFirm::query()
            ->from(StrategyPlacementFirm::TABLE . ' as t1')
            ->leftJoin(NetworkFirm::TABLE . ' as t2', 't2.id', '=', 't1.nw_firm_id')
            ->select([
                't1.id', 't1.placement_id', 't1.str_placement_id', 't1.nw_firm_id', 't2.name as nw_firm_name', 't1.nw_cache_time',
                't1.nw_timeout', 't1.ad_data_nw_timeout', 't1.ad_up_status', 't1.nw_offer_requests',
                't1.bid_token_cache_time', 't1.header_bidding_timeout', 't1.click_sync', 't1.show_sync', 't1.create_time',
                't1.update_time', 't1.status'
            ])
            ->where('t1.str_placement_id', $strategyPlacementId)
            ->where('t1.status', '>', 0)
            ->orderByDesc('t1.id')
            ->paginate($request->query('page_size', 10), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        return $this->jsonResponse($data);
    }

    public function create(Request $request): JsonResponse
    {
        $strategyPlacementId = $request->query('str_pl_id', -1);

        $strategyPlacement = StrategyPlacement::query()
            ->from(StrategyPlacement::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->leftJoin(App::TABLE . ' as t3', 't3.id', '=', 't2.app_id')
            ->select(['t2.format', 't3.platform'])
            ->where('t1.id', '=', $strategyPlacementId)
            ->firstOrFail();


        /* 寻找尚未配置的广告位厂商策略 */
        $nwFirmId = 0;
        $existedFirmStrategyIds = StrategyPlacementFirm::query()
            ->where('str_placement_id', $strategyPlacementId)
            ->orderBy('nw_firm_id')
            ->get(['nw_firm_id'])
            ->toArray();
        $existedFirmStrategyIds = array_column($existedFirmStrategyIds, 'nw_firm_id');
        for ($i = 1, $iMax = count(NetworkFirm::getNwFirmMap()); $i <= $iMax; $i++) {
            if (!in_array($i, $existedFirmStrategyIds, true)) {
                $nwFirmId = $i;
                break;
            }
        }

        $nwTimeOut = 5;
        /* 对于激励视频和插屏，IOS 设置为 8，安卓设置为 16 */
        if (in_array($strategyPlacement['format'], [Placement::FORMAT_RV, Placement::FORMAT_INTERSTITIAL], false)) {
            $nwTimeOut = $strategyPlacement['platform'] == App::PLATFORM_ANDROID ? 12 : 8;
        }

        return $this->jsonResponse([
            'nw_firm_id'             => $nwFirmId,
            'nw_cache_time'          => 1800,
            'nw_timeout'             => $nwTimeOut,
            'ad_data_nw_timeout'     => -1,
            'ad_up_status'           => 900,
            'nw_offer_requests'      => 1,
            'header_bidding_timeout' => 3000,
            'bid_token_cache_time'   => 1800,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $rules = [
            'str_pl_id' => ['required', 'exists:' . StrategyPlacement::TABLE . ',id'],
            'nw_firm_id' => [
                'required',
                'exists:' . NetworkFirm::TABLE . ',id',
                new NotExists(StrategyPlacementFirm::TABLE, 'nw_firm_id', [], 'str_placement_id = ' . $request->get('str_pl_id'))
            ],
        ];

        $this->validate($request, $rules);

        $placementId = StrategyPlacement::query()->where('id', $request->get('str_pl_id'))->value('placement_id');

        $insertRes = StrategyPlacementFirm::query()->insert([
            'placement_id'           => $placementId,
            'str_placement_id'       => $request->get('str_pl_id'),
            'nw_firm_id'             => $request->get('nw_firm_id'),
            'nw_cache_time'          => $request->get('nw_cache_time', 0),
            'nw_timeout'             => $request->get('nw_timeout', 0),
            'ad_data_nw_timeout'     => $request->get('ad_data_nw_timeout', -1),
            'ad_up_status'           => $request->get('ad_up_status', 900),
            'nw_offer_requests'      => $request->get('nw_offer_requests', 0),
            'header_bidding_timeout' => $request->get('header_bidding_timeout', 500),
            'bid_token_cache_time'   => $request->get('bid_token_cache_time', 1800),
            'create_time'            => time(),
            'update_time'            => time(),
            'status'                 => StrategyPlacementFirm::STATUS_STOP,
        ]);


        if ($insertRes) {
            $this->triggerPlacementEvent($placementId);
        }

        return $this->jsonResponse();
    }

    public function show($id): JsonResponse
    {
        $strategy = StrategyPlacementFirm::query()->where('id', $id)->firstOrFail();

        return $this->jsonResponse($strategy);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $strategy = StrategyPlacementFirm::query()->where('id', $id)->firstOrFail();

        $input = $request->input();
        $data = [
            'status' => $request->input('status', $strategy['status']),
            'update_time' => time(),
//            'header_bidding_timeout' => 500
        ];
        if (isset($input['nw_cache_time'])) {
            $data['nw_cache_time'] = $input['nw_cache_time'];
        }
        if (isset($input['nw_timeout'])) {
            $data['nw_timeout'] = $input['nw_timeout'];
        }
        if (isset($input['nw_offer_requests'])) {
            $data['nw_offer_requests'] = $input['nw_offer_requests'];
        }
        if (isset($input['header_bidding_timeout'])) {
            $data['header_bidding_timeout'] = $input['header_bidding_timeout'];
        }
        if(isset($input['ad_data_nw_timeout'])){
            $data['ad_data_nw_timeout'] = $input['ad_data_nw_timeout'];
        }
        if(isset($input['ad_up_status'])){
            $data['ad_up_status'] = $input['ad_up_status'];
        }
        if(isset($input['bid_token_cache_time'])){
            $data['bid_token_cache_time'] = $input['bid_token_cache_time'];
        }

        $updateRes = $strategy->update($data);

        if ($updateRes !== false) {
            $this->triggerPlacementEvent($strategy['placement_id']);
        }

        return $this->jsonResponse();
    }

    /**
     * 触发 Placement 更新的事件
     * 注：仅复制，未进行任何修改
     *
     * @param $placementId
     */
    private function triggerPlacementEvent($placementId)
    {
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
            Log::info("update placement quque success");
        }

        //触发更新placement的事件
        Event::fire(new UpdatePlacement($placementId));
    }
}
