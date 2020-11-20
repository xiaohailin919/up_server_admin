<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class MGroup extends MySqlBase
{
    protected $table = 'mgroup';

    const STATUS_RUNNING = 3;
    
    public function getRank($id)
    {
        if($id <= 0){
            return 0;
        }
        $rank = $this->getOne(['rank'], ['id' => $id]);
        if(empty($rank)){
            return 0;
        }
        return $rank['rank'];
    }
}