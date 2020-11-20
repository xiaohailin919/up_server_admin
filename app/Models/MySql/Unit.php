<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class Unit extends MySqlBase
{
    protected $table = 'unit';
    const TABLE = 'unit';
    
    const DELIVERY_STOP = 0;
    const DELIVERY_RUNNING = 1;
    
    const CRAWL_STATUS_DONE = 1;
    const CRAWL_STATUS_CRAWLING = 2;
    const CRAWL_STATUS_FAILURE = 3;

    const STATUS_DELETED = 0;
    const STATUS_LOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;
    
    /**
     * 通过unit ID获取对应的nw_firm_id
     * @param int $id
     * @return int
     */
    public function getFirmIdById($id)
    {
        if($id <= 0){
            return 0;
        }
        $networkId = $this->queryBuilder()->where('id', $id)->value('network_id');
        if($networkId <= 0){
            return 0;
        }
        $networkModel = new Network();
        $firmId = $networkModel->queryBuilder()->where('id', $networkId)->value('nw_firm_id');
        if($networkId <= 0){
            return 0;
        }
        return $firmId;
    }
}