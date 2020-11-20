<?php
/**
 * Created by PhpStorm.
 * User: zengzhihai
 * Date: 2019/9/4
 * Time: 11:11
 */


namespace App\Http\Controllers;

use App\Models\MySql\Segment;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\MySql\UnitPriorityLog as UnitPriorityLogModel;
use App\Models\MySql\App as AppModel;
use App\Models\MySql\Publisher as PublisherModel;
use App\Models\MySql\Placement as PlacementModel;

class UnitChangeLogController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('page_size', 15);
        $offset = ($request->query('page_no', 1) - 1) * $limit;

        /* 对于两个 uuid，先根据 uuid 查出 id，不建立多列索引，因为难以优化成最左前缀原则支持的形式，让 MySql 自动使用 index merge 的方式 */
        $query = UnitPriorityLogModel::query()
            ->from(DB::raw(UnitPriorityLogModel::TABLE . ' as t1'))
            ->leftJoin(PublisherModel::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(PlacementModel::TABLE . ' as t3', 't3.id', '=', 't1.placement_id')
            ->leftJoin(AppModel::TABLE . ' as t4', 't4.id', '=', 't3.app_id')
            ->leftJoin(Segment::TABLE . ' as t5', 't5.id', '=', 't1.segment_id')
            ->select([
                't1.id', 't1.publisher_id', 't2.name as publisher_name', 't2.email as publisher_email',
                't4.id as app_id', 't4.uuid as app_uuid', 't4.name as app_name', 't1.placement_id as placement_id', 't3.uuid as placement_uuid', 't3.name as placement_name',
                't1.unit_id', 't1.traffic_group_id', 't1.segment_id', 't5.uuid as segment_uuid', 't1.ip', 't1.create_time'
            ])
            ->orderByDesc('t1.id');

        if ($request->has('publisher_id')) {
            $query->where('t1.publisher_id', $request->query('publisher_id'));
        }
        if ($request->has('placement_id')) {
            /* 分步查询来支持 uuid 和 id 同时搜索，不要采用联表的 where ... or where 操作，否则性能太差 */
            $placementId = PlacementModel::query()->where('uuid', $request->query('placement_id'))->value('id');
            $placementId = $placementId == null ? $request->query('placement_id') : $placementId;
            $query->where('t3.id', $placementId);
        }
        if ($request->has('traffic_group_id')) {
            $query->where('t1.traffic_group_id', $request->query('traffic_group_id'));
        }
        if ($request->has('segment_id')) {
            $segmentId = Segment::query()->where('uuid', $request->query('segment_id'))->value('id');
            $segmentId = $segmentId == null ? $request->query('segment_id') : $segmentId;
            $query->where('t1.segment_id', $segmentId);
        }
        if ($request->has('start_time') && $request->has('end_time')) {
            $query->whereBetween('t1.create_time', [$request->query('start_time'), $request->query('end_time')]);
        }
        $bindings = $query->getBindings();
        $sql = str_replace('?', '%s', $query->toSql());
        $sql = sprintf($sql, ...$bindings);
        /* 如果没有搜索条件，则直接用 id 来翻页 */
        if (!str_contains($sql, 'where')) {
            /* 取最高 id 作为总数，同时采用 id 计算解决 offset 问题 */
            $total = UnitPriorityLogModel::query()->count(['*']);
            $top = $total - $offset;
            $data = $query->whereBetween('t1.id', [$top - $limit, $top])->get()->toArray();
            $data = [
                'total' => $total,
                'page_no' => (int)$request->get('page_no', 1),
                'page_size' => (int)$request->get('page_size', 15),
                'list' => $data
            ];
        } else {
            $paginator = $query->paginate($request->query('page_size'), ['*'], 'page_no', $request->query('page_no', 1));
            $data = $this->parseResByPaginator($paginator);
        }

        return $this->jsonResponse($data);
    }


    public function show($id): JsonResponse
    {
        /* data 作为总数据 */
        $data = UnitPriorityLogModel::query()
            ->from(UnitPriorityLogModel::TABLE . ' as t1')
            ->leftJoin(PublisherModel::TABLE . ' as t2', 't2.id', '=', 't1.publisher_id')
            ->leftJoin(PlacementModel::TABLE . ' as t3', 't3.id', '=', 't1.placement_id')
            ->leftJoin(AppModel::TABLE . ' as t4', 't4.id', '=', 't3.app_id')
            ->select([
                't1.id', 't1.segment_id', 't1.traffic_group_id', 't1.ip', 't1.unit_id', 't1.create_time',
                't1.publisher_id', 't2.name as publisher_name', 't2.email as email',
                't1.placement_id', 't3.uuid as placement_uuid', 't3.name as placement_name',
                't4.id as app_id', 't4.uuid as app_uuid', 't4.name as app_name',
                't1.log'
            ])
            ->where('t1.id', $id)->firstOrFail()->toArray();


        /* 原纪录，是 Data 的拷贝，但不需要保存所有信息 */
        $nowRecord = [
            'id'               => $data['id'],
            'unit_id'          => $data['unit_id'],
            'publisher_id'     => $data['publisher_id'],
            'placement_id'     => $data['placement_id'],
            'traffic_group_id' => $data['traffic_group_id'],
            'segment_id'       => $data['segment_id'],
            'ip'               => $data['ip'],
            'create_time'      => $data['create_time'],
            'log'              => json_decode($data['log'], true),
        ];
        unset($data['log']);

        /* 把同一个 placement_id + traffic_group_id + segment_id 的最近两次数据做比较，找出数据不同的字段，组成当次 Change Logs 的 json 字符串 */
        /* 获取和 $nowRecord 相同的信息即可 */
        $preRecord = UnitPriorityLogModel::query()
            ->select(['id', 'unit_id', 'publisher_id', 'placement_id', 'traffic_group_id', 'segment_id', 'ip', 'create_time', 'log'])
            ->where('id', '<', $nowRecord['id'])
            ->where('publisher_id', $nowRecord['publisher_id'])
            ->where('placement_id', $nowRecord['placement_id'])
            ->where('traffic_group_id', $nowRecord['traffic_group_id'])
            ->where('segment_id', $nowRecord['segment_id'])
            ->orderByDesc('id')
            ->first();

        $preRecord = $preRecord == null ? ['log' => '[]'] : $preRecord->toArray();
        $preRecord['log'] = json_decode($preRecord['log'], true);

        $data['now_record_original'] = $nowRecord;
        $data['pre_record_original'] = $preRecord;

        /* 剔除新旧记录相同的项目 */
        foreach ($preRecord as $key => $value) {
            if ($nowRecord[$key] == $preRecord[$key]) {
                unset($nowRecord[$key], $preRecord[$key]);
            }
        }
        /* 剔除 log 里面相同的项目 */
        if (!empty($nowRecord['log'])) {
            /* 遍历每一个广告源记录 */
            foreach ($nowRecord['log'] as $idx => $adSource) {
                if (is_array($adSource)) {
                    /* 遍历该广告源的每一个字段 */
                    foreach ($adSource as $adSourceKey => $adSourceValue) {
                        if (isset($preRecord['log'][$idx][$adSourceKey]) && $nowRecord['log'][$idx][$adSourceKey] == $preRecord['log'][$idx][$adSourceKey]) {
                            unset($preRecord['log'][$idx][$adSourceKey], $nowRecord['log'][$idx][$adSourceKey]);
                        }
                    }
                    /* 如果空了的话，去掉 */
                    if (empty($preRecord['log'][$idx])) {
                        unset($preRecord['log'][$idx]);
                    }
                    if (empty($nowRecord['log'][$idx])) {
                        unset($nowRecord['log'][$idx]);
                    }
                } else if (isset($preRecord['log'][$idx]) && $nowRecord['log'][$idx] == $preRecord['log'][$idx]) {
                    unset($nowRecord['log'][$idx], $preRecord['log'][$idx]);
                }
            }
        }

        if (isset($nowRecord['log']) && count($nowRecord['log']) == 0) {
            unset($nowRecord['log']);
        }
        if (isset($nowRecord['log']) && count($preRecord['log']) == 0) {
            unset($preRecord['log']);
        }

        $data['now_record'] = $nowRecord;
        $data['pre_record'] = $preRecord;

        return $this->jsonResponse($data);
    }
}