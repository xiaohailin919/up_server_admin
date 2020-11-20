<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class TcStrategy extends Base
{

    protected $table = 'tc_strategy';
    const TABLE = 'tc_strategy';
    protected $guarded = ['id'];

    const PLATFORM_ANDROID_FOREIGN = 1;
    const PLATFORM_IOS = 2;
    const PLATFORM_ANDROID_DOMESTIC = 3;
    const PLATFORM_ANDROID = 0;

    const RULE_TYPE_PLATFORM  = 1;
    const RULE_TYPE_APP       = 2;
    const RULE_TYPE_PLACEMENT = 3;

    const APP_UNSET = 0;
    const PLACEMENT_UNSET = 0;
    const NETWORK_FIRM_ID_ALL = 0;

    const TYPE_SYNC_IMPRESSION_TO_PLUGIN = 1;
    const TYPE_SYNC_CLICK_TO_PLUGIN = 2;
    const TYPE_SYNC_IMPRESSION_TO_QCC = 3;
    const TYPE_SYNC_CLICK_TO_QCC = 4;

    const RATE_NEVER = 0;
    const RATE_ALWAYS = 100;

    const STATUS_ALL = -1;
    const STATUS_CLOSE = 1;
    const STATUS_ACTIVE = 3;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public static function getRuleTypeMap(): array {
        return [
            self::RULE_TYPE_PLATFORM => 'Platform',
            self::RULE_TYPE_APP => 'App',
            self::RULE_TYPE_PLACEMENT => 'Placement'
        ];
    }


    public static function getPlatformMap(): array
    {
        return [
            self::PLATFORM_ANDROID_FOREIGN  => '国外 Android',
            self::PLATFORM_IOS              => 'IOS',
            self::PLATFORM_ANDROID_DOMESTIC => '国内 Android',
        ];
    }

    /**
     * 获取所有插件类型的 Map
     * @return array
     */
    public static function getTypeMap(): array
    {
        return [
            self::TYPE_SYNC_IMPRESSION_TO_PLUGIN => '展示给插件',
            self::TYPE_SYNC_CLICK_TO_PLUGIN      => '点击给插件',
            self::TYPE_SYNC_IMPRESSION_TO_QCC    => '展示给QCC',
            self::TYPE_SYNC_CLICK_TO_QCC         => '点击给QCC',
        ];
    }

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_CLOSE  => '关闭'
        ];
    }

}
