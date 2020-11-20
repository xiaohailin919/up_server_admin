<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategyFirm extends Base
{
    protected $table = 'strategy_firm';
    const TABLE = 'strategy_firm';

    const ALL_PLACEMENT = 0;
    const ALL_FORMAT = -1;
    const ALL_NW_FIRM = 0;
    const ALL_PLATFORM = 0;
    const ALL_STATUS = 0;
    const ALL_RULE_TYPE = 0;
    const ALL_PUBLISHER = 0;

    const RULE_TYPE_PLATFORM = 1;
    const RULE_TYPE_PLACEMENT = 2;

    const STATUS_UNSET = 0;
    const STATUS_STOP = 1;
    const STATUS_ACTIVE = 3;

    protected $guarded = ['id'];

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 获取状态 Map
     *
     * @return array
     */
    public static function getStatusMap(): array
    {
        return [self::STATUS_STOP => '暂停', self::STATUS_ACTIVE => '启用'];
    }

}