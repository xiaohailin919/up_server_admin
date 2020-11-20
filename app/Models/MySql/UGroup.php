<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class UGroup extends MySqlBase
{
    protected $table = 'ugroup';

    const STATUS_RUNNING = 3;
    const OPT_SWITCH_OFF = 0;
    const OPT_SWITCH_ON = 1;
    const OPT_STATUS_NOT_OPTIMIZED = 1;
    const OPT_STATUS_OPTIMIZING = 2;
    const OPT_STATUS_OPTIMIZED = 3;
    
    public function getOptLists()
    {
        $query = $this->queryBuilder();
        $lists = $query->where('opt_switch', 1)
                ->orderBy('opt_time', 'asc')
                ->get();
        return $lists;
    }
}