<?php


namespace App\Models\MySql;


class NetworkFirmMediaBuyer extends Base
{
    protected $table = 'network_firm_media_buyer';
    const TABLE = 'network_firm_media_buyer';

    const CRAWL_SUPPORT_DAY_YES = 2;
    const CRAWL_SUPPORT_DAY_NO = 1;
    const CRAWL_SUPPORT_HOUR_YES = 2;
    const CRAWL_SUPPORT_HOUR_NO = 1;

    const STATUS_ACTIVE = 3;
    const STATUS_BLOCK = 1;

    public function getStatusMap(): array
    {
        return [
            self::STATUS_BLOCK => '停止',
            self::STATUS_ACTIVE => '正常',
        ];
    }
}