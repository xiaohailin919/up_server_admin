<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class Queue extends MySqlBase
{
    const OBJECT_TYPE_PUBLISHER = 0;
    const OBJECT_TYPE_APP       = 1;
    const OBJECT_TYPE_PLACEMENT = 2;
    const STATUS_WAITING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 2;
    
    protected $table = 'queue';
    
    public function getAppWaiting()
    {
        return $this->getWaiting(self::OBJECT_TYPE_APP);
    }
    
    public function getPlacementWaiting()
    {
        return $this->getWaiting(self::OBJECT_TYPE_PLACEMENT);
    }
    
    public function getPublisherWaiting()
    {
        return $this->getWaiting(self::OBJECT_TYPE_PUBLISHER);
    }

    private function getWaiting($objectType)
    {
        $where = [
            'object_type' => $objectType,
            'status' => self::STATUS_WAITING,
        ];
        return $this->get(['id', 'object_id'], $where);
    }

    public function addAppWaiting($objectId, $adminId = 0)
    {
        return $this->addWaiting(self::OBJECT_TYPE_APP, $objectId, $adminId);
    }

    public function addPlacementWaiting($objectId, $adminId = 0)
    {
        return $this->addWaiting(self::OBJECT_TYPE_PLACEMENT, $objectId, $adminId);
    }

    public function addPublisherWaiting($objectId, $adminId = 0)
    {
        return $this->addWaiting(self::OBJECT_TYPE_PUBLISHER, $objectId, $adminId);
    }

    private function addWaiting($objectType, $objectId, $adminId = 0)
    {
        $insert = [
            'object_type' => $objectType,
            'object_id' => $objectId,
            'admin_id' => $adminId,
            'publisher_id' => 0,
            'retry_times' => 0,
            'create_time' => time(),
            'status' => self::STATUS_WAITING,
        ];
        return $this->queryBuilder()->insert($insert);
    }

    public function updateToSuccess($queueId)
    {
        return $this->updateTo($queueId, self::STATUS_SUCCESS);
    }

    public function updateToFailure($queueId)
    {
        return $this->updateTo($queueId, self::STATUS_FAILURE);
    }

    private function updateTo($queueId, $status)
    {
        $where = [
            'id' => $queueId
        ];
        $update = [
            'status' => $status,
            'sync_time' => time(),
        ];
        return $this->queryBuilder()->where($where)->update($update);
    }
}