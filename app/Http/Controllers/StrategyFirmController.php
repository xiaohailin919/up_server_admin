<?php

namespace App\Http\Controllers;

use App\Models\MySql\NetworkFirm;
use App\Models\MySql\Publisher;
use App\Models\MySql\Users;
use DB;
use Illuminate\Http\Request;

use App\Models\MySql\StrategyFirm;
use App\Models\MySql\App;
use App\Models\MySql\Placement;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;


class StrategyFirmController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = StrategyFirm::query()
            ->from(StrategyFirm::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->leftJoin(App::TABLE . ' as t3', 't3.id', '=', 't2.app_id')
            ->leftJoin(Publisher::TABLE . ' as t4', 't4.id', '=', 't2.publisher_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t5', 't5.id', '=', 't1.nw_firm_id')
            ->leftJoin(Users::TABLE . ' as t6', 't6.id', '=', 't1.admin_id')
            ->select([
                't1.id', 't1.placement_id', 't2.uuid as placement_uuid', 't2.name as placement_name',
                't3.uuid as app_uuid', 't3.name as app_name', 't4.id as publisher_id', 't4.name as publisher_name',
                't1.platform', 't1.format', 't1.nw_firm_id', 't5.name as nw_firm_name', 't1.status',
                't6.id as admin_id', 't6.name as admin_name', 't1.update_time'
            ])->orderByDesc('t1.update_time');

        if ($request->has('placement_id')) {
            $tmp = $request->query('placement_id');
            if (is_numeric($tmp) && $tmp == 0) {
                $query->where('t1.placement_id', 0);
            } else {
                $query->where(static function ($subQuery) use ($tmp) {
                    $subQuery->where('t2.id', $tmp)->orWhere('t2.uuid', $tmp);
                });
            }
        }
        if ($request->has('app_id')) {
            $tmp = $request->query('app_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            });
        }
        if ($request->has('publisher_id')) {
            $query->where('t4.id', $request->query('publisher_id'));
        }
        if (array_key_exists($request->query('format', -1), Placement::getFormatMap())) {
            $query->where('t1.format', $request->query('format'));
        }
        if (array_key_exists($request->query('platform', -1), App::getPlatformMap())) {
            $query->where('t1.platform', $request->query('platform'));
        }
        if ($request->query('nw_firm_id', -1) >= 0) {
            $query->where('t1.nw_firm_id', $request->query('nw_firm_id'));
        }
        if (array_key_exists($request->query('status', -1), StrategyFirm::getStatusMap())) {
            $query->where('t1.status', $request->query('status'));
        }

        $paginate = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));

        $data = $this->parseResByPaginator($paginate);

        foreach ($data['list'] as $idx => $datum) {
            $data['list'][$idx]['nw_firm_name']   = $datum['nw_firm_name'] ?? '';
            $data['list'][$idx]['placement_name'] = $datum['placement_name'] ?? '';
            $data['list'][$idx]['placement_uuid'] = $datum['placement_uuid'] ?? '';
            $data['list'][$idx]['app_name']       = $datum['app_name'] ?? '';
            $data['list'][$idx]['app_uuid']       = $datum['app_uuid'] ?? '';
            $data['list'][$idx]['publisher_name'] = $datum['publisher_name'] ?? '';
        }

        return $this->jsonResponse($data);
    }

    public function store(Request $request): JsonResponse
    {
        $placementUuidIdMap = array_column(Placement::query()->get(['id', 'uuid'])->toArray(), 'id', 'uuid');
        $placementUuidIdMap[0] = 0;
        $nwFirmIdList = NetworkFirm::query()->get(['id'])->getQueueableIds();
        $nwFirmIdList[] = 0;
        $rules = [
            'placement_uuid'       => ['required', Rule::in(array_keys($placementUuidIdMap))],
            'platform_list'        => ['required_if:placement_uuid,0', 'array'],
            'platform_list.*'      => [Rule::in(array_keys(App::getPlatformMap()))],
            'format_list'          => ['required_if:placement_uuid,0', 'array'],
            'format_list.*'        => [Rule::in(array_keys(Placement::getFormatMap()))],
            'nw_firm_id_list'      => ['required', 'array'],
            'nw_firm_id_list.*'    => [Rule::in($nwFirmIdList)],
            'status'               => ['required', Rule::in(array_keys(StrategyFirm::getStatusMap()))],
            'unit_fill_rate_range' => ['required', 'array'],
            'unit_fill_rate_range.*.range'     => ['required', 'array'],
            'unit_fill_rate_range.*.range.*'   => ['integer', 'between:0, 10000'],
            'unit_fill_rate_range.*.intervals' => ['required', 'integer', 'min:0'],
        ];
        $this->validate($request, $rules);

        $placementId       = $placementUuidIdMap[$request->get('placement_uuid')];
        $nwFirmIdList      = $request->get('nw_firm_id_list');
        $platformList      = $request->get('platform_list');
        $formatList        = $request->get('format_list');
        $status            = $request->get('status');
        $unitFillRateRange = $request->get('unit_fill_rate_range');
        $adminId           = auth('api')->id();

        /* 广告平台传入了所有，但是传入了不止一个，返回参数错误 */
        if (count($nwFirmIdList) > 1 && in_array(0, $nwFirmIdList, false)) {
            return $this->jsonResponse(['placement_id_list' => '所输入的 nw_firm_id 无效'], 10000);
        }

        /* 如果指定广告位，则广告样式和系统平台根据广告位获取 */
        if ($placementId != 0) {
            $placement = Placement::query()
                ->from(Placement::TABLE . ' as t1')
                ->leftJoin(App::TABLE . ' as t2', 't2.id', '=', 't1.app_id')
                ->where('t1.id', $placementId)
                ->firstOrFail(['t1.format', 't2.platform']);
            $platformList = [$placement['platform']];
            $formatList   = [$placement['format']];
        }

        try {
            DB::beginTransaction();

            /* 三层循环添加 */
            foreach ($nwFirmIdList as $nwFirmId) {
                foreach ($platformList as $platform) {
                    foreach ($formatList as $format) {
                        if (StrategyFirm::query()
                            ->where('platform', $platform)
                            ->where('placement_id', $placementId)
                            ->where('format', $format)
                            ->where('nw_firm_id', $nwFirmId)
                            ->exists()
                        ) {
                            DB::rollBack();
                            return $this->jsonResponse([
                                'platform' => $platform,
                                'placement_id' => $placementId,
                                'format' => $format,
                                'nw_firm_id' => $nwFirmId
                            ], 10000, "相同记录已存在");
                        }
                        StrategyFirm::query()->insert([
                            'platform'             => $platform,
                            'placement_id'         => $placementId,
                            'format'               => $format,
                            'nw_firm_id'           => $nwFirmId,
                            'status'               => $status,
                            'unit_fill_rate_range' => json_encode($unitFillRateRange),
                            'admin_id'             => $adminId,
                            'create_time'          => date('Y-m-d H:i:s'),
                            'update_time'          => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->transactionExceptionResponse($e);
        }

        return $this->jsonResponse();
    }

    public function show($id): JsonResponse
    {
        $strategy = StrategyFirm::query()
            ->from(StrategyFirm::TABLE . ' as t1')
            ->leftJoin(Placement::TABLE . ' as t2', 't2.id', '=', 't1.placement_id')
            ->leftJoin(NetworkFirm::TABLE . ' as t3', 't3.id', '=', 't1.nw_firm_id')
            ->select([
                't1.id', 't1.platform', 't1.format', 't2.uuid as placement_uuid', 't1.nw_firm_id',
                't3.name as nw_firm_name', 't1.unit_fill_rate_range', 't1.status'
            ])
            ->where('t1.id', $id)->firstOrFail();
        $strategy['nw_firm_name']         = $strategy['nw_firm_name'] ?? '';
        $strategy['placement_uuid']       = $strategy['placement_uuid'] ?? '';
        $strategy['unit_fill_rate_range'] = json_decode($strategy['unit_fill_rate_range'], true);
        return $this->jsonResponse($strategy);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'status'               => ['required', Rule::in(array_keys(StrategyFirm::getStatusMap()))],
            'unit_fill_rate_range' => ['required', 'array'],
            'unit_fill_rate_range.*.range'     => ['required', 'array'],
            'unit_fill_rate_range.*.range.*'   => ['integer', 'between:0, 10000'],
            'unit_fill_rate_range.*.intervals' => ['required', 'integer', 'min:0'],
        ];
        $this->validate($request, $rules);

        $strategy = StrategyFirm::query()->where('id', $id)->firstOrFail();
        $strategy->update([
            'status'               => $request->get('status'),
            'unit_fill_rate_range' => json_encode($request->get('unit_fill_rate_range')),
            'admin_id'             => auth('api')->id(),
            'update_time'          => date('Y-m-d H:i:s'),
        ]);
        return $this->jsonResponse();
    }
}
