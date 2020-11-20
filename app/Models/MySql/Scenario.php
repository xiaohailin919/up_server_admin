<?php

namespace App\Models\MySql;


class Scenario extends Base
{
    const TABLE = 'scenario';

    const STATUS_BLOCKED = 1;
    const STATUS_RUNNING = 3;

    protected $table = 'scenario';
    const TYPE = 'scenario';

    protected $guarded = ['id'];

    /**
     * 生成UUID
     * @return string
     */
    public static function generateUuid()
    {
        return 'f' . uniqid();
    }
}