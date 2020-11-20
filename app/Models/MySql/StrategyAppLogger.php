<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use DateTime;

class StrategyAppLogger extends Base
{
    protected $table = 'strategy_app_logger';

    public $timestamps = true;

    const DA_KEY_SEP = "\r\n";

    const RULE_TYPE_APP = 1;
    const RULE_TYPE_PUBLISHER_GROUP = 2;

    const STATUS_PAUSED = 1;
    const STATUS_ACTIVE = 0;

    const TK_TIMER_SWITCH_OFF = 1;
    const TK_TIMER_SWITCH_ON  = 2;

    const TCP_TK_DA_TYPE_SINGLE = 1;
    const TCP_TK_DA_TYPE_BOTH = 2;

    protected $guarded = [
        'id'
    ];

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 数据库时间格式转换
     *
     * @param DateTime|int $value 从数据库读取则为 Timestamp 类型，框架写入则为 int 类型
     * @return DateTime|false|int|string
     */
    public function fromDateTime($value) {
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }

    /**
     * 获取所有状态码映射配置
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /**
     * Tk timer switch map
     * @return array
     */
    public function getTkTimerSwitchMap(){
        return [
            self::TK_TIMER_SWITCH_OFF => 'Off',
            self::TK_TIMER_SWITCH_ON  => 'On',
        ];
    }

    public static function getTypeMap(): array {
        return [
            self::RULE_TYPE_APP => 'APP',
            self::RULE_TYPE_PUBLISHER_GROUP => 'Publisher Group',
        ];
    }
}
