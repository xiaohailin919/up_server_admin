<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class Network extends MySqlBase
{
    protected $table = 'network';
    const TABLE = 'network';

    const STATUS_DELETED = 0;
    const STATUS_LOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;


    public function getFirmId($id)
    {
        $data = $this->getOne(['nw_firm_id'], ['id' => $id]);
        return $data['nw_firm_id'];
    }
}