<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class MGroupRule extends MySqlBase
{
    protected $table = 'mgroup_rule';

    const TYPE_AREA = 0;
    const TYPE_TIME = 1;
    const TYPE_DAY = 2;
    const TYPE_NETWORK = 3;
    const TYPE_HOUR = 4;
    const TYPE_CUSTOM = 5;
    
    const RULE_INCLUDE = 0;
    const RULE_EXCLUDE = 1;
    const RULE_GTE = 2;
    const RULE_LTE = 3;
    const RULE_RANGE_IN = 4;
    const RULE_RANGE_NOT_IN = 5;
    const RULE_CUSTOM = 6;
    
    const STATUS_STOP = 0;
    const STATUS_RUNNING = 1;
    
    public function getRule($gid)
    {
        if($gid <= 0){
            return [];
        }
        $rule = $this->get([], ['group_id' => $gid, 'status' => self::STATUS_RUNNING]);
        return $rule;
    }
}