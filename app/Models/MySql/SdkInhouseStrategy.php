<?php


namespace App\Models\MySql;


class SdkInhouseStrategy extends Base
{
    protected $table = 'sdk_inhouse_strategy';
    const TABLE = 'sdk_inhouse_strategy';

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
            self::STATUS_DELETE => '已删除'
        ];
    }
}