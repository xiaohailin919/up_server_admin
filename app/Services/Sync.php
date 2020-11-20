<?php

namespace App\Services;

use App\Models\Mongo\Publisher as PublisherMoModel;
use App\Models\Mongo\App as AppMoModel;
use App\Models\Mongo\Placement as PlacementMoModel;

class Sync
{
    protected $moModel = [];
    protected $idField = 0;
    protected $objectType = '';

    public function __construct($objectType)
    {
        if ($objectType == 'publisher') {
            $this->objectType = 'publisher';
            $this->moModel = new PublisherMoModel();
            $this->idField = 'publisher_id';

        } elseif ($objectType == 'app') {
            $this->objectType = 'app';
            $this->moModel = new AppMoModel();
            $this->idField = 'app_id';
        } elseif ($objectType == 'placement') {
            $this->objectType = 'placement';
            $this->moModel = new PlacementMoModel();
            $this->idField = 'placement_id';
        }
    }

    public function handle($objectId, $data)
    {
        if (!$data) {
            return false;
        }

        return $this->save($objectId, $data);
    }

    /**
     * 保存数据到mongo
     * @param $objectId
     * @param $data
     * @return bool
     */
    private function save($objectId, $data)
    {
        if ($this->isNew($objectId)) {
            return $this->moModel->queryBuilder()->insert($data);
        } else {
            $update = $data;
            unset($update[$this->idField]);

            $original = $this->moModel->queryBuilder()->select(array_keys($data))->where([$this->idField => $objectId])->first();

            if ($this->isEqual($update, $original)) {
                return 1;
            }
            return $this->moModel->queryBuilder()->where([$this->idField => $objectId])->update($update);
        }
    }

    /**
     * 是否是新的object id
     * @param $objectId
     * @return bool
     */
    private function isNew($objectId)
    {
        $idField = $this->idField;
        $model = $this->moModel;

        $one = $model->getOne([$idField], [$idField => $objectId]);

        return $one ? false : true;
    }

    /**
     * 判断待更新的值是否与原有值相等
     * @param $new
     * @param $original
     * @return bool
     */
    private function isEqual($new, $original)
    {
        $original2 = [];
        foreach ($new as $k => $v) {
            if (isset($original[$k])) {
                $original2[$k] = $original[$k];
            }
        }

        if (serialize($original2) == serialize($new)) {
            return true;
        }
        return false;
    }
}