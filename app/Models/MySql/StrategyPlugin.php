<?php

namespace App\Models\MySql;

class StrategyPlugin extends Base
{
    protected $table = 'strategy_plugin';
    const TABLE = 'strategy_plugin';

    protected $guarded = ['id'];

    const STATUS_DELETED = 0;
    const STATUS_STOP = 2;
    const STATUS_RUNNING = 3;

    public static function getStatusMap() {
        return [
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_STOP => 'Pending',
            self::STATUS_RUNNING => 'Running',
        ];
    }
}
