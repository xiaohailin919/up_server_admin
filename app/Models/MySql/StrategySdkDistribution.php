<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategySdkDistribution extends Base
{

    const TYPE_PUBLISHER = 1;
    const TYPE_GROUP = 2;

    const STATUS_ACTIVE = 3;
    const STATUS_STOP = 1;

    protected $table = 'strategy_sdk_distribution';

    protected $fillable = [
        'type', 'publisher_id', 'publisher_group_id', 'android', 'ios', 'unity_android', 'unity_ios', 'unity_android_ios', 'admin_id', 'status'
    ];

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_PUBLISHER => 'Publisher',
            self::TYPE_GROUP => 'Publisher Group'
        ];
    }
}
