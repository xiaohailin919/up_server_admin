<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class NetworkFirm extends Base
{
    protected $table = 'network_firm';
    const TABLE = 'network_firm';

    public $timestamps = true;

    protected $guarded = [];

    const TYPE_NORMAL = 1;
    const TYPE_ONLINE_API = 2;

    const CRAWL_SUPPORT_DAY_NO   = 0;
    const CRAWL_SUPPORT_DAY_YES  = 1;
    const CRAWL_SUPPORT_HOUR_NO  = 1;
    const CRAWL_SUPPORT_HOUR_YES = 2;

    const CURRENCY_CNY = 'CNY';
    const CURRENCY_JPY = 'JPY';
    const CURRENCY_USD = 'USD';

    /**
     * 自定义广告厂商边界值，一切自定义广告厂商 ID 都大于此值
     */
    const CUSTOM_NW_FIRM_BOUNDARY = 100000;

    const FACEBOOK     = 1 ;
    const ADMOB        = 2 ;
    const INMOBI       = 3 ;
    const FLURRY       = 4 ;
    const APPLOVIN     = 5 ;
    const MINTEGRAL    = 6 ;
    const MOPUB        = 7 ;
    const TENCENT      = 8 ;
    const CHARTBOOST   = 9 ;
    const TAPJOY       = 10;
    const IRONSOURCE   = 11;
    const UNITY        = 12;
    const VUNGLE       = 13;
    const ADCOLONY     = 14;
    const TOUTIAO      = 15;
    const UNIPLAY      = 16;
    const ONEWAY       = 17;
    const MOBPOWER     = 18;
    const KSYUN        = 19;
    const YEAHMOBI     = 20;
    const APPNEXT      = 21;
    const BAIDU        = 22;
    const NEND         = 23;
    const MAIO         = 24;
    const STARTAPP     = 25;
    const SUPERAWESOME = 26;
    const LUOMI        = 27;
    const KUAISHOU     = 28;
    const SIGMOB       = 29;
    const SMAATO       = 30;
    const FIVELINE     = 31;
    const MYTARGET     = 32;
    const GOOGLE       = 33;
    const YANDEX       = 34;
    const MYOFFER      = 35;
    const OGURY        = 36;
    const FYBER        = 37;
    const VPLAY        = 38;
    const HUAWEI       = 39;
    const HELIUM       = 40;
    const ADX          = 66;

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }


    public static function getFirmIdFormatMap(): array
    {
        $tmpData = self::query()->get(['id', 'native', 'rewarded_video', 'banner', 'interstitial', 'splash']);
        $firmIdFormatMap = [];
        foreach ($tmpData as $key => $val) {
            $firmIdFormatMap[$val['id']] =
                [
                    $val['native'], $val['rewarded_video'], $val['banner'], $val['interstitial'], $val['splash']
                ];
        }
        return $firmIdFormatMap;
    }

    public function getAllNetworkFirmId()
    {
        return array_column(self::query()->get(['id'])->toArray(), 'id');
    }

    /**
     * 获取支持的货币列表
     *
     * @return array
     */
    public static function getCurrencyList(): array
    {
        return [self::CURRENCY_CNY, self::CURRENCY_USD, self::CURRENCY_JPY];
    }

    /**
     * 获取货币与货币符号映射表
     *
     * @return array|string[]
     */
    public function getCurrencySymbolMap(): array
    {
        return [
            self::CURRENCY_CNY => 'RMB¥',
            self::CURRENCY_USD => '$',
            self::CURRENCY_JPY => '¥'
        ];
    }

    /**
     * 获取 Network Firm 表中所有的开发者 ID -> Name 映射表
     *
     * @return array
     */
    public static function getNetworkPublisherIdNameMap(): array
    {
        /* 过滤掉所有默认厂商，因为 publisher id 为 0 且在数组最前端的时候 php 会自动将数组转成索引数组 😂 */
        $records = self::query()->leftJoin('publisher as p', 'p.id', '=', 'network_firm.publisher_id')
            ->select('network_firm.publisher_id')
            ->selectRaw('IFNULL(p.name, "UNKNOWN") as name')
            ->groupBy(['network_firm.publisher_id', 'p.name'])
            ->where('network_firm.publisher_id', '!=', 0)
            ->get()->toArray();
        $res = array_column($records, 'name', 'publisher_id');
        $res[0] = 'All Publisher';
        return $res;
    }

    /**
     * 获取自定义 Network Firm 与 Publisher 的 ID => 厂商 - 开发者映射表
     * @return array
     */
    public static function getCustomNwIdNameWithPublisherMap(): array
    {
        $records = self::query()->leftJoin('publisher as p', 'p.id', '=', 'network_firm.publisher_id')
            ->select('network_firm.id', 'network_firm.name', 'network_firm.publisher_id')
            ->selectRaw('IFNULL(p.name, "UNKNOWN") as publisher_name')
            ->where('network_firm.id', '>', self::CUSTOM_NW_FIRM_BOUNDARY)
            ->where('network_firm.publisher_id', '!=', 0)
            ->get()->toArray();
        $res = [];
        foreach ($records as $record) {
            $res[$record['id']] = [
                'name' => $record['name'],
                'publisher_name' => $record['publisher_name'],
                'publisher_id' => $record['publisher_id']
            ];
        }
        return $res;
    }
}