<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Placement;
use App\Models\MySql\TcStrategy;
use App\Models\MySql\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class TcStrategyController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $subQuery = TcStrategy::query()
            ->groupBy(['platform_type', 'app_id', 'placement_id', 'nw_firm_id'])
            ->select(['platform_type', 'app_id', 'placement_id', 'nw_firm_id'])
            ->selectRaw('MAX(id)                        AS id')
            ->selectRaw('MAX(admin_id)                  AS admin_id')
            ->selectRaw('MAX(update_time)               AS update_time')
            ->selectRaw('MAX(status)                    AS status')
            ->selectRaw('SUM(IF(`type` = 1, `rate`, 0)) AS impression_to_plugin')
            ->selectRaw('SUM(IF(`type` = 2, `rate`, 0)) AS click_to_plugin')
            ->selectRaw('SUM(IF(`type` = 3, `rate`, 0)) AS impression_to_qcc')
            ->selectRaw('SUM(IF(`type` = 4, `rate`, 0)) AS click_to_qcc');
        $query = TcStrategy::query()
            ->from(DB::raw("({$subQuery->toSql()}) as t1"))
            ->leftJoin(App::TABLE         . ' as t2', 't2.id', '=', 't1.app_id')
            ->leftJoin(Placement::TABLE   . ' as t3', 't3.id', '=', 't1.placement_id')
            ->leftJoin(Users::TABLE       . ' as t4', 't4.id', '=', 't1.admin_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t5', 't5.id', '=', 't1.nw_firm_id')
            ->select('t1.*',
                't2.uuid as app_uuid',
                't2.name as app_name',
                't3.uuid as placement_uuid',
                't3.name as placement_name',
                't5.name as nw_firm_name',
                't4.name as admin_name',
                't2.platform as app_platform'
            )->orderByDesc('t1.update_time');

        if ($request->has('app_id')) {
            $tmp = $request->query('app_id');
            $query->where(static function($innerQuery) use ($tmp) {
                $innerQuery->where('t2.id', $tmp)->orWhere('t2.uuid', $tmp);
            });
        }
        if ($request->has('app_name')) {
            $query->where('t2.name', 'like', '%' . $request->query('app_name') . '%');
        }
        if ($request->has('placement_id')) {
            $tmp = $request->query('placement_id');
            $query->where(static function ($innerQuery) use ($tmp) {
                $innerQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            });
        }
        if ($request->has('placement_name')) {
            $query->where('t3.name', 'like', '%' . $request->query('placement_name') . '%');
        }
        if ($request->query('nw_firm_id', -1) > -1) {
            $query->where('t1.nw_firm_id', $request->query('nw_firm_id'));
        }
        if (array_key_exists($request->query('rule_type', -1), TcStrategy::getRuleTypeMap())) {
            switch ($request->query('rule_type')) {
                case TcStrategy::RULE_TYPE_PLATFORM:  $query->where('t1.app_id', 0);break;
                case TcStrategy::RULE_TYPE_APP:       $query->where('t1.app_id', '!=', 0)->where('t1.placement_id', 0);break;
                case TcStrategy::RULE_TYPE_PLACEMENT: $query->where('t1.placement_id', '!=', 0);break;
            }
        }
        if (array_key_exists($request->query('status', -1), TcStrategy::getStatusMap())) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginator);

        foreach ($data['list'] as $idx => $datum) {

            if ($datum['app_id'] != 0) {
                $data['list'][$idx]['platform_type'] = $datum['app_platform'] == App::PLATFORM_IOS
                    ? TcStrategy::PLATFORM_IOS : TcStrategy::PLATFORM_ANDROID;
            }

            $data['list'][$idx]['rule_type']      = $this->calcRuleType($datum['app_id'], $datum['placement_id']);
            $data['list'][$idx]['app_uuid']       = $datum['app_uuid'] ?? '-';
            $data['list'][$idx]['app_name']       = $datum['app_name'] ?? '-';
            $data['list'][$idx]['placement_uuid'] = $datum['placement_uuid'] ?? '-';
            $data['list'][$idx]['placement_name'] = $datum['placement_name'] ?? '-';
            $data['list'][$idx]['admin_name']     = $datum['admin_name'] ?? '-';

            unset(
                $data['list'][$idx]['app_id'],
                $data['list'][$idx]['placement_id'],
                $data['list'][$idx]['nw_firm_id'],
                $data['list'][$idx]['app_platform']
            );
        }

        return $this->jsonResponse($data);
    }

    public function show($id): JsonResponse
    {
        $strategy = TcStrategy::query()
            ->from(TcStrategy::TABLE . ' as t1')
            ->leftJoin(App::TABLE . ' as t2', 't2.id', '=', 't1.app_id')
            ->leftJoin(Placement::TABLE . ' as t3', 't3.id', '=', 't1.placement_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t4', 't4.id', '=', 't1.nw_firm_id')
            ->select([
                't1.id', 't1.platform_type', 't1.app_id', 't2.uuid as app_uuid', 't2.name as app_name',
                't1.placement_id', 't3.uuid as placement_uuid', 't3.name as placement_name',
                't1.nw_firm_id', 't4.name as nw_firm_name', 't1.status'
            ])->where('t1.id', $id)->firstOrFail();

        $strategies = TcStrategy::query()
            ->select(['type', 'rate'])
            ->where('platform_type', $strategy['platform_type'])
            ->where('app_id', $strategy['app_id'])
            ->where('placement_id', $strategy['placement_id'])
            ->where('nw_firm_id', $strategy['nw_firm_id'])
            ->get()->toArray();
        $typeValueMap = array_column($strategies, 'rate', 'type');

        $strategy['rule_type']            = $this->calcRuleType($strategy['app_id'], $strategy['placement_id']);
        $strategy['impression_to_plugin'] = $typeValueMap[TcStrategy::TYPE_SYNC_IMPRESSION_TO_PLUGIN];
        $strategy['click_to_plugin']      = $typeValueMap[TcStrategy::TYPE_SYNC_CLICK_TO_PLUGIN];
        $strategy['impression_to_qcc']    = $typeValueMap[TcStrategy::TYPE_SYNC_IMPRESSION_TO_QCC];
        $strategy['click_to_qcc']         = $typeValueMap[TcStrategy::TYPE_SYNC_CLICK_TO_QCC];

        return $this->jsonResponse($strategy);
    }

    public function store(Request $request): JsonResponse
    {
        $nwFirmIdList = NetworkFirm::query()->get(['id'])->getQueueableIds();
        $nwFirmIdList[] = 0;
        $rules = [
            'rule_type'             => ['required', Rule::in(array_keys(TcStrategy::getRuleTypeMap()))],
            'platform_type'         => ['required_if:rule_type,1', Rule::in(array_keys(TcStrategy::getPlatformMap()))],
            'app_uuid_list'         => ['required_if:rule_type,2', 'array'],
            'app_uuid_list.*'       => ['required_if:rule_type,2', 'exists:app,uuid', 'distinct'],
            'placement_uuid_list'   => ['required_if:rule_type,3', 'array'],
            'placement_uuid_list.*' => ['required_if:rule_type,3', 'exists:placement,uuid', 'distinct'],
            'nw_firm_id_list'       => ['required', 'array'],
            'nw_firm_id_list.*'     => ['required', Rule::in($nwFirmIdList), 'distinct'],
            'impression_to_plugin'  => ['required', 'integer', 'min:0', 'max:100'],
            'click_to_plugin'       => ['required', 'integer', 'min:0', 'max:100'],
            'impression_to_qcc'     => ['required', 'integer', 'min:0', 'max:100'],
            'click_to_qcc'          => ['required', 'integer', 'min:0', 'max:100'],
            'status'                => ['required', Rule::in(array_keys(TcStrategy::getStatusMap()))],
        ];
        $this->validate($request, $rules);

        $ruleType = $request->get('rule_type');
        $nwFirmIdList = $request->get('nw_firm_id_list');
        $platformType = 0;
        $appPlacementsMap = [];
        switch ($ruleType) {
            /* 厂商默认规则，不需要指定 APP、Placement 信息 */
            case TcStrategy::RULE_TYPE_PLATFORM:
                $platformType = $request->get('platform_type');
                $appPlacementsMap[0] = [0];
                break;
            /* App 规则，platform_type 为 0，无需指定 Placement 信息 */
            case TcStrategy::RULE_TYPE_APP:
                $appIdList = App::query()->select(['id'])->whereIn('uuid', $request->get('app_uuid_list'))->get()->getQueueableIds();
                foreach ($appIdList as $appId) {
                    $appPlacementsMap[$appId] = [0];
                }
                break;
            /* Placement 规则，platform_type 为 0，所有信息都需指定 */
            case TcStrategy::RULE_TYPE_PLACEMENT:
                $placementList = Placement::query()->select(['id', 'app_id'])->whereIn('uuid', $request->get('placement_uuid_list'))->get()->toArray();
                foreach ($placementList as $placement) {
                    $appPlacementsMap[$placement['app_id']][] = $placement['id'];
                }
                break;
            default: echo "Impossible! Just for good appearance.";
        }

        $typeValueMap = [
            TcStrategy::TYPE_SYNC_IMPRESSION_TO_PLUGIN => $request->get('impression_to_plugin'),
            TcStrategy::TYPE_SYNC_CLICK_TO_PLUGIN      => $request->get('click_to_plugin'),
            TcStrategy::TYPE_SYNC_IMPRESSION_TO_QCC    => $request->get('impression_to_qcc'),
            TcStrategy::TYPE_SYNC_CLICK_TO_QCC         => $request->get('click_to_qcc'),
        ];
        $status = $request->get('status');

        try {
            DB::beginTransaction();

            /* 边判断边插入 */
            foreach ($nwFirmIdList as $nwFirmId) {
                foreach ($appPlacementsMap as $appId => $placementIdList) {
                    foreach ($placementIdList as $placementId) {
                        if (TcStrategy::query()
                            ->where('platform_type', $platformType)
                            ->where('app_id', $appId)
                            ->where('placement_id', $placementId)
                            ->where('nw_firm_id', $nwFirmId)
                            ->exists()
                        ) {
                            return $this->jsonResponse([], 10000, '策略已存在');
                        }

                        /* 每一个需要插入四条记录 */
                        foreach ($typeValueMap as $type => $value) {
                            TcStrategy::query()->insert([
                                'platform_type' => $platformType,
                                'app_id'        => $appId,
                                'placement_id'  => $placementId,
                                'nw_firm_id'    => $nwFirmId,
                                'type'          => $type,
                                'rate'          => $value,
                                'admin_id'      => auth('api')->id(),
                                'create_time'   => date('Y-m-d H:i:s'),
                                'update_time'   => date('Y-m-d H:i:s'),
                                'status'        => $status,
                            ]);
                        }

                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }

        return $this->jsonResponse([], 1);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'impression_to_plugin'  => ['required', 'integer', 'min:0', 'max:100'],
            'click_to_plugin'       => ['required', 'integer', 'min:0', 'max:100'],
            'impression_to_qcc'     => ['required', 'integer', 'min:0', 'max:100'],
            'click_to_qcc'          => ['required', 'integer', 'min:0', 'max:100'],
            'status'                => ['required', Rule::in(array_keys(TcStrategy::getStatusMap()))],
        ];
        $this->validate($request, $rules);

        $strategy = TcStrategy::query()->where('id', $id)->firstOrFail();

        $typeValueMap = [
            TcStrategy::TYPE_SYNC_IMPRESSION_TO_PLUGIN => $request->get('impression_to_plugin'),
            TcStrategy::TYPE_SYNC_CLICK_TO_PLUGIN      => $request->get('click_to_plugin'),
            TcStrategy::TYPE_SYNC_IMPRESSION_TO_QCC    => $request->get('impression_to_qcc'),
            TcStrategy::TYPE_SYNC_CLICK_TO_QCC         => $request->get('click_to_qcc'),
        ];
        $status = $request->get('status');

        try {
            DB::beginTransaction();

            foreach ($typeValueMap as $type => $rate) {
                TcStrategy::query()
                    ->where('platform_type', $strategy['platform_type'])
                    ->where('app_id', $strategy['app_id'])
                    ->where('placement_id', $strategy['placement_id'])
                    ->where('nw_firm_id', $strategy['nw_firm_id'])
                    ->where('type', $type)
                    ->update([
                        'rate'        => $rate,
                        'admin_id'    => auth('api')->id(),
                        'update_time' => date('Y-m-d H:i:s'),
                        'status'      => $status,
                    ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }

        return $this->jsonResponse();
    }

    /**
     * 计算 Rule Type
     *
     * @param $appId
     * @param $placementId
     * @return int
     */
    private function calcRuleType($appId, $placementId): int
    {
        if (empty($appId)) {
            return TcStrategy::RULE_TYPE_PLATFORM;
        }
        if (empty($placementId)) {
            return TcStrategy::RULE_TYPE_APP;
        }
        return TcStrategy::RULE_TYPE_PLACEMENT;
    }
}
