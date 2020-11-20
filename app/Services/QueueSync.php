<?php

namespace App\Services;

use App\Models\MySql\Queue as QueueMyModel;
use Illuminate\Support\Facades\Auth;

class QueueSync
{
    protected $objectType = '';
    const MAX_RETRY_TIMES = 3;

    public function __construct($objectType)
    {
        if ($objectType == 'publisher') {
            $this->objectType = 'publisher';
        } elseif ($objectType == 'app') {
            $this->objectType = 'app';
        } elseif ($objectType == 'placement') {
            $this->objectType = 'placement';
        }
    }

    public function handle($queueId, $objectId, $data)
    {
        $queueMyModel = new QueueMyModel();
        $where = [
            'id' => $queueId
        ];
        $origin = $queueMyModel->getOne(['retry_times'], $where);
        if (!$origin) {
            return false;
        }

        if ($origin['retry_times'] > self::MAX_RETRY_TIMES) { //大于最大重试次数,状态置为失败
            return $queueMyModel->updateToFailure($queueId);

        } else {
            $sync = new Sync($this->objectType);
            $res = $sync->handle($objectId, $data);

            if ($res) { // 成功,队列状态置为成功
                return $queueMyModel->updateToSuccess($queueId);

            } else { //失败,重试次数加1
                $update = [
                    'retry_times' => $origin['retry_times'] + 1,
                    'sync_time' => time(),
                ];
                return $queueMyModel->queryBuilder()->where($where)->update($update);
            }
        }
    }

    public function dispatch($objectId)
    {
        $adminId = Auth::id();
        if (!$adminId) {
            return false;
        }

        $funDict = [
            'app' => 'addAppWaiting',
            'placement' => 'addPlacementWaiting',
            'publisher' => 'addPublisherWaiting',
        ];

        if (!isset($funDict[$this->objectType])) {
            return false;
        }

        $queueMyModel = new QueueMyModel();
        return call_user_func_array(array($queueMyModel, $funDict[$this->objectType]), array($objectId, $adminId));
    }
}