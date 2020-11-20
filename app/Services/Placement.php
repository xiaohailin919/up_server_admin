<?php

namespace App\Services;

use App\Models\MySql\Placement as PlacementMyModel;
use App\Models\MySql\Publisher as PublisherMyModel;
use App\Models\MySql\App as AppMyModel;
use App\Models\MySql\StrategyPlacement;
use App\Models\MySql\StrategyPlacementFirm as StrategyPlacementFirmMyModel;
use App\Models\Mongo\Placement as PlacementMoModel;
use App\Services\Sync as Sync;
use App\Services\QueueSync as QueueSync;
use App\Services\MGroup as MGroupService;

class Placement
{
    /**
     * 全量同步
     * @param int $id
     * @return boolean
     */
    public static function sync($id)
    {
        $data = self::buildSyncData($id);
        $sync = new Sync('placement');
        return $sync->handle($id, $data);
    }
    
    /**
     * 通过队列增量同步
     * @param int $queueId
     * @param int $id
     * @return boolean
     */
    public static function queueSync($queueId, $id)
    {
        $data = self::buildSyncData($id);
        $sync = new QueueSync('placement');
        return $sync->handle($queueId, $id, $data);
    }
    
    /**
     * 构造待同步数据
     * @param int $id
     * @return array
     */
    private static function buildSyncData($id)
    {
        $field = [
            'id as placement_id',
            'uuid as placement_uuid',
            'publisher_id',
            'app_id',
            'name',
            'format',
            'status',
            'private_status'
        ];
        
        $PlacementMyModel = new PlacementMyModel();
        $publisherMyModel = new PublisherMyModel();
        $appMyModel = new AppMyModel();
        
        $mGroupService = new MGroupService();
        
        $placement = $PlacementMyModel->getOne($field, ['id' => $id]);
        if(empty($placement)){
            return [];
        }
        $strategyPlField = [
            'id',
            'cap_hour',
            'cap_day',
            'pacing',
            'show_type',
            'wifi_autoplay',
            'nw_requests',
            'nw_cache_time',
            'nw_timeout',
            'nw_offer_requests',
            'refresh',
            'auto_refresh',
            'auto_refresh_time',
            'delivery'
        ];
        $strategy = (array)StrategyPlacement::query()
            ->where('placement_id', $id)
            ->where('status', StrategyPlacement::STATUS_RUNNING)
            ->select($strategyPlField)
            ->first();
        if(!empty($strategy)){
            $strategyPlFirmMyModel = new StrategyPlacementFirmMyModel();
            $strategyPlFirm = $strategyPlFirmMyModel->getStrategyByStrPlId($strategy['id']);
            $strategy['firm'] = $strategyPlFirm;
        }
        $onePublisher = $publisherMyModel->getOne(["api_key", 'system'],['id' => $placement['publisher_id']]);
        $placement['api_key'] = !empty($onePublisher['api_key']) ? $onePublisher['api_key'] : '';
        $system = !empty($onePublisher['system']) ? $onePublisher['system'] : '';
        $placement["system"] = $system;

        $placement['app_uuid'] = $appMyModel->getUuid($placement['app_id']);
        $placement['mgroup'] = $mGroupService->getMGroupByPid($placement['placement_id'], $system);
        $placement['strategy'] = $strategy;
        $placement['update_time'] = time();

        if ($placement['status'] == PlacementMyModel::STATUS_RUNNING
            && $placement['private_status'] == PlacementMyModel::PRIVATE_STATUS_RUNNING) {

            $placement['status'] = PlacementMoModel::STATUS_RUNNING;

        } else {
            $placement['status'] = PlacementMoModel::STATUS_STOP;
        }
        unset($placement['private_status']);

        return $placement;
    }
}