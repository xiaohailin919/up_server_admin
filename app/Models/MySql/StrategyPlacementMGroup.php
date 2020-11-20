<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class StrategyPlacementMGroup extends MySqlBase
{
    const STATUS_STOP = 0;
    const STATUS_RUNNING = 1;
    
    protected $table = 'strategy_placement_mgroup';

    protected $guarded = ['id'];
    
    public $timestamps = true;
}