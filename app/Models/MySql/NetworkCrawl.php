<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class NetworkCrawl extends Base
{
    protected $table = 'network_crawl_job_config';
    const TABLE = 'network_crawl_job_config';

    protected $fillable = [
        'network_firm_type', 'type', 'schedule_time', 'pull_type',
        'nw_firm_id', 'admin_id', 'create_time', 'update_time', 'status'
    ];

    /**
     * 买量
     */
    const NW_FIRM_TYPE_MEDIA_BUY = 1;
    /**
     * 广告变现
     */
    const NW_FIRM_TYPE_MONETIZATION  = 2;

    const TYPE_UNSET = 0;
    const TYPE_DAY = 1;
    const TYPE_HOUR = 2;

    const PULL_YESTERDAY = 1;
    const PULL_BEFORE_YESTERDAY = 2;

    const STATUS_UP = 3;

    private static $nwFirmTypeMap;

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_DAY => '天维度',
            self::TYPE_HOUR => '小时维度'
        ];
    }

    public static function getPullTypeMap(): array
    {
        return [
            self::PULL_YESTERDAY => '昨天',
            self::PULL_BEFORE_YESTERDAY => '前天'
        ];
    }

    public function getStatusMap(): array
    {
        return [
            self::STATUS_UP => '开启',
        ];
    }

    public static function getNwFirmTypeMap(): array {
        return [
            self::NW_FIRM_TYPE_MEDIA_BUY => '买量',
            self::NW_FIRM_TYPE_MONETIZATION => '变现',
        ];
    }


    public static function getNwFirmTypeName($type) {
        if (self::$nwFirmTypeMap == null) {
            self::$nwFirmTypeMap = self::getNwFirmTypeMap();
        }
        return self::$nwFirmTypeMap[$type] ?? '-';
    }
}
