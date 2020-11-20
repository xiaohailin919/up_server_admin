<?php


namespace App\Models\MySql;


class SdkChannel extends Base
{
    protected $table = 'sdk_channel';
    const TABLE = 'sdk_channel';

    const STATUS_STOP   = 1;
    const STATUS_ACTIVE = 3;
    
    /**
     * 状态映射列表
     * @return array
     */
    public static function getStatusMap(): array
    {
        return [
            self::STATUS_STOP   => '停用',
            self::STATUS_ACTIVE => '启用',
        ];
    }
}