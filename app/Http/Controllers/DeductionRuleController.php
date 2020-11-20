<?php

namespace App\Http\Controllers;

use App\Models\MySql\App;
use App\Models\MySql\DeductionRule;
use App\Models\MySql\Placement;
use App\Models\MySql\Publisher;
use App\Models\MySql\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeductionRuleController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = DeductionRule::query()
            ->from(DeductionRule::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(App::TABLE       . ' as t3', 't3.id', '=', 't1.app_id')
            ->leftJoin(Placement::TABLE . ' as t4', 't4.id', '=', 't1.placement_id')
            ->leftJoin(Users::TABLE     . ' as t5', 't5.id', '=', 't1.admin_id')
            ->select([
                't1.id', 't1.type', 't1.publisher_id', 't2.name as publisher_name',
                't1.app_id', 't3.name as app_name', 't3.uuid as app_uuid',
                't1.placement_id', 't4.name as placement_name', 't4.uuid as placement_uuid',
                't1.discount', 't1.status', 't1.admin_id', 't5.name as admin_name'
            ])
            ->selectRaw("IF(t1.update_time='0000-00-00 00:00:00', t1.create_time, t1.update_time) as update_time ")
            ->where('t1.type', $request->get('type', DeductionRule::TYPE_IMPRESSION))
            ->orderByDesc('t1.create_time');

        $defaultRequired = true;
        if ($request->has('publisher_id')) {
            $defaultRequired = $request->query('publisher_id') == 0;
            $query->where('t2.id', $request->query('publisher_id'));
        }
        if ($request->has('app_id')) {
            $defaultRequired = false;
            $tmp = $request->query('app_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t3.id', $tmp)->orWhere('t3.uuid', $tmp);
            });
        }
        if ($request->has('placement_id')) {
            $defaultRequired = false;
            $tmp = $request->query('placement_id');
            $query->where(static function ($subQuery) use ($tmp) {
                $subQuery->where('t4.id', $tmp)->orWhere('t4.uuid', $tmp);
            });
        }
        if (array_key_exists($request->query('status', -1), DeductionRule::getStatusMap())) {
            $defaultRequired = false;
            $query->where('t1.status', $request->query('status'));
        }
        switch ($request->query('dimension')) {
            case DeductionRule::DIMENSION_PUBLISHER:
                $query->where('t1.app_id', 0);
                break;
            case DeductionRule::DIMENSION_APP:
                $query->where('t1.app_id', '!=', 0)->where('t1.placement_id', 0);
                break;
            case DeductionRule::DIMENSION_PLACEMENT:
                $query->where('t1.placement_id', '!=', 0);
                break;
            default:
        }

        $paginator = $query->paginate($request->query('page_size', 15), ['*'], 'page_no', $request->query('page_no', 1));
        $data = $this->parseResByPaginator($paginator);

        $defaultRecord = [
            'id'             => 0,
            'type'           => $request->get('type', DeductionRule::TYPE_IMPRESSION),
            'publisher_id'   => 0,
            'publisher_name' => '-',
            'app_id'         => 0,
            'app_name'       => '-',
            'app_uuid'       => '-',
            'placement_id'   => 0,
            'placement_name' => '-',
            'placement_uuid' => '-',
            'discount'       => 100,
            'status'         => DeductionRule::STATUS_ACTIVE,
            'update_time'    => 0,
            'admin_id'       => 0,
            'admin_name'     => '默认'
        ];

        if ($defaultRequired) {
            /* 追加一条默认记录 */
            $data['total']++;
            if ($data['page_size'] * ($data['page_no'] - 1) < $data['total'] && count($data['list']) != $data['page_size']) {
                $data['list'][] = $defaultRecord;
            }
        }

        foreach ($data['list'] as $idx => $datum) {
            $data['list'][$idx]['admin_name']     = $datum['admin_name'] ?? '-';
            $data['list'][$idx]['app_name']       = $datum['app_name'] ?? '-';
            $data['list'][$idx]['app_uuid']       = $datum['app_uuid'] ?? '-';
            $data['list'][$idx]['placement_uuid'] = $datum['placement_uuid'] ?? '-';
            $data['list'][$idx]['placement_name'] = $datum['placement_name'] ?? '-';
            $data['list'][$idx]['discount']       = $datum['discount'] / 100;
            $data['list'][$idx]['update_time']    = $datum['update_time'] > 0 ? $datum['update_time'] : 0;
        }

        return $this->jsonResponse($data);
    }

    public function store(Request $request): JsonResponse
    {
        $publisherIdList = Publisher::query()->get(['id'])->getQueueableIds();
        $publisherIdList[] = 0;
        $rules = [
            'type'           => ['required', Rule::in(array_keys(DeductionRule::getTypeMap()))],
            'dimension'      => ['required', Rule::in(array_keys(DeductionRule::getDimensionMap()))],
            'publisher_id'   => ['required_if:dimension,' . DeductionRule::DIMENSION_PUBLISHER, Rule::in($publisherIdList)],
            'app_uuid'       => ['required_if:dimension,' . DeductionRule::DIMENSION_APP, 'exists:app,uuid'],
            'placement_uuid' => ['required_if:dimension,' . DeductionRule::DIMENSION_PLACEMENT, 'exists:placement,uuid'],
            'discount'       => ['required', 'integer', 'min:0'],
            'status'         => ['required', Rule::in(array_keys(DeductionRule::getStatusMap()))]
        ];
        $this->validate($request, $rules);

        $placementId = $appId = $publisherId = 0;
        switch ($request->get('dimension')) {
            case DeductionRule::DIMENSION_PLACEMENT:
                $placement = Placement::query()->where('uuid', $request->get('placement_uuid'))->firstOrFail(['id', 'app_id', 'publisher_id'])->toArray();
                $placementId = $placement['id'];
                $appId       = $placement['app_id'];
                $publisherId = $placement['publisher_id'];
                break;
            case DeductionRule::DIMENSION_APP:
                $app = App::query()->where('uuid', $request->get('app_uuid'))->firstOrFail(['id', 'publisher_id'])->toArray();
                $appId       = $app['id'];
                $publisherId = $app['publisher_id'];
                break;
            case DeductionRule::DIMENSION_PUBLISHER:
                $publisherId = $request->get('publisher_id');
                break;
        }

        /* 判断是否唯一冲突 */
        $type = $request->get('type');
        if (DeductionRule::query()
            ->where('type', $type)
            ->where('publisher_id', $publisherId)
            ->where('app_id', $appId)
            ->where('placement_id', $placementId)
            ->exists()
        ) {
            return $this->jsonResponse([], 10000, "Record already exists!");
        }

        DeductionRule::query()->insert([
            'type'         => $type,
            'publisher_id' => $publisherId,
            'app_id'       => $appId,
            'placement_id' => $placementId,
            'discount'     => $request->get('discount'),
            'admin_id'     => auth('api')->id(),
            'create_time'  => date('Y-m-d H:i:s'),
            'update_time'  => date('Y-m-d H:i:s'),
            'status'       => $request->get('status'),
        ]);

        return $this->jsonResponse([], 1);
    }

    public function show($id): JsonResponse
    {
        $record = DeductionRule::query()
            ->from(DeductionRule::TABLE . ' as t1')
            ->leftJoin(Publisher::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(App::TABLE       . ' as t3', 't3.id', '=', 't1.app_id')
            ->leftJoin(Placement::TABLE . ' as t4', 't4.id', '=', 't1.placement_id')
            ->select([
                't1.id', 't1.type', 't1.publisher_id', 't2.name as publisher_name',
                't1.app_id', 't3.name as app_name', 't3.uuid as app_uuid',
                't1.placement_id', 't4.name as placement_name', 't4.uuid as placement_uuid',
                't1.discount', 't1.status'
            ])
            ->where('t1.id', $id)
            ->firstOrFail()->toArray();

        $record['app_uuid']       = $record['app_uuid']       ?? '';
        $record['app_name']       = $record['app_name']       ?? '';
        $record['placement_uuid'] = $record['placement_uuid'] ?? '';
        $record['placement_name'] = $record['placement_name'] ?? '';

        if (empty($record['app_uuid'])) {
            $record['dimension'] = DeductionRule::DIMENSION_PUBLISHER;
        } else if (empty($record['placement_uuid'])) {
            $record['dimension'] = DeductionRule::DIMENSION_APP;
        } else {
            $record['dimension'] = DeductionRule::DIMENSION_PLACEMENT;
        }

        return $this->jsonResponse($record);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'discount' => ['required', 'integer', 'min:0'],
            'status'   => ['required', Rule::in(array_keys(DeductionRule::getStatusMap()))],
        ];
        $this->validate($request, $rules);

        $record = DeductionRule::query()->where('id', $id)->firstOrFail();
        $record->update([
            'discount'    => $request->get('discount'),
            'status'      => $request->get('status'),
            'admin_id'    => auth('api')->id(),
            'update_time' => date('Y-m-d H:i:s'),
        ]);

        return $this->jsonResponse();
    }
}
