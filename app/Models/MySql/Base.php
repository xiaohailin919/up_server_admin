<?php

namespace App\Models\MySql;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    // Format
    const FORMAT_NATIVE       = 0;
    const FORMAT_RV           = 1;
    const FORMAT_BANNER       = 2;
    const FORMAT_INTERSTITIAL = 3;
    const FORMAT_SPLASH       = 4;

    // Platform
    const PLATFORM_ANDROID = 1;
    const PLATFORM_IOS     = 2;

    // System（仅用 1）
    const SYSTEM_UP = 1;
    const SYSTEM_AM = 2;

    // Status
    const STATUS_DELETE = 99;

    const INCLUDE = 1;
    const EXCLUDE = 2;

    const EXPORT_NOT = 1;
    const EXPORT_YES = 2;

    const SORT_DESC = 1;
    const SORT_ASC  = 2;

    private static $formatMap;
    private static $allNwFirmMap;
    private static $platformMap;
    private static $systemMap;

    /**
     * 默认使用时间戳戳功能
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 获取当前时间
     *
     * @return DateTime|int
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 避免转换时间戳为时间字符串
     *
     * @param DateTime|int $value
     * @return DateTime|int
     */
    public function fromDateTime($value) {
        return $value;
    }

    /**
     * 获取广告类型 Map
     * @return array
     */
    public static function getFormatMap(): array
    {
        return [
            self::FORMAT_NATIVE       => 'Native',
            self::FORMAT_RV           => 'Rewarded Video',
            self::FORMAT_BANNER       => 'Banner',
            self::FORMAT_INTERSTITIAL => 'Interstitial',
            self::FORMAT_SPLASH       => 'Splash',
        ];
    }

    /**
     * 根据广告类型 id 获取广告类型名字
     * @param $format
     * @return mixed|null 若无则返回 null
     */
    public static function getFormatName($format) {
        if (self::$formatMap == null) {
            self::$formatMap = self::getFormatMap();
        }
        return self::$formatMap[$format] ?? '';
    }

    /**
     * @return string[] 获取系统平台 Map
     */
    public static function getPlatformMap(): array {
        return [
            self::PLATFORM_ANDROID => 'Android',
            self::PLATFORM_IOS => 'IOS',
        ];
    }

    /**
     * 根据系统平台 id 获取系统类型名字
     * @param $platform
     * @return mixed|null 若无则返回 null
     */
    public static function getPlatformName($platform) {
        if (self::$platformMap == null) {
            self::$platformMap = self::getPlatformMap();
        }
        return self::$platformMap[$platform] ?? '';
    }

    /**
     * 获取广告平台 Map，不包含 MyOffer 和自定义广告平台
     *
     * @return array|null
     */
    public static function getNwFirmMap() {
        $data = NetworkFirm::query()
            ->select('id', 'name')
            ->where('id', '!=', NetworkFirm::MYOFFER)
            ->where('id', '<=', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->get()
            ->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 获取所有聚合的广告平台，包含 MyOffer
     * @return array
     */
    public static function getAllIntegratedNwFirmMap(): array {
        $data = NetworkFirm::query()
            ->where('publisher_id', '=', 0)
            ->get(['id', 'name'])->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 获取所有自定义广告平台
     * @return array
     */
    public static function getCustomNwFirmMap(): array {
        $data = NetworkFirm::query()
            ->where('id', '>', NetworkFirm::CUSTOM_NW_FIRM_BOUNDARY)
            ->get(['id', 'name'])->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 获取所有的广告平台，不进行任何过滤
     * @return array
     */
    public static function getAllNwFirmMap(): array {
        $data = NetworkFirm::query()->get(['id', 'name'])->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 获取所有买量平台
     * @return array
     */
    public static function getMediaBuyNwFirmMap(): array {
        $data = NetworkFirmMediaBuyer::query()->get(['id', 'name'])->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 获取所有广告平台，包括买量与变现平台
     * @return array
     */
    public static function getAllMonetizationMediaBuyNwFirmMap(): array {
        $tmpMap = self::getAllIntegratedNwFirmMap();
        $mediaBuyNwFirmMap = self::getMediaBuyNwFirmMap();
        foreach ($mediaBuyNwFirmMap as $id => $name) {
            $tmpMap[$id] = $name;
        }
        return $tmpMap;
    }

    /**
     * 根据广告平台 id 获取广告平台名字
     * @param $nwFirm
     * @return mixed|null 若无则返回 null
     */
    public static function getNwFirmName($nwFirm) {
        if (self::$allNwFirmMap == null) {
            self::$allNwFirmMap = self::getAllNwFirmMap();
        }
        return self::$allNwFirmMap[$nwFirm]?? '';
    }

    /**
     * 获取SYSTEM map
     * @return array
     */
    public static function getSystemMap(): array {
        return [
            self::SYSTEM_UP => 'UP',
            self::SYSTEM_AM => 'AM'
        ];
    }

    /**
     * 获取system name
     * @param $system
     * @return mixed
     */
    public static function getSystemName($system) {
        if (self::$systemMap == null) {
            self::$systemMap = self::getSystemMap();
        }
        return self::$systemMap[$system] ?? '';
    }

    /** 获取包含 / 不包含列表
     * @return array|int[]
     */
    public static function getIncludeExcludeList(): array {
        return [
            self::INCLUDE,
            self::EXCLUDE,
        ];
    }

    /**
     * 仅仅只是为了兼容已废弃的 MySqlBase 的代码，每见到这个调用，请改成：
     * Model::query();
     *
     * @deprecated
     */
    public function queryBuilder() {
        return self::query();
    }

    /**
     * 仅仅只是为了兼容已废弃的 MySqlBase 的代码，每见到这个调用，请舍弃掉
     *
     * @deprecated
     * @param array $fieldList
     * @param array $where
     * @return mixed
     */
    public function get(array $fieldList, array $where = [])
    {
        $query = self::query()->select($fieldList);
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query->get();
    }

    /**
     * 仅仅只是为了兼容已废弃的 MySqlBase 的代码，每见到这个调用，请舍弃掉
     *
     * @deprecated
     * @param array $fieldList
     * @param array $where
     * @return \Illuminate\Database\Eloquent\Builder|Model|null
     */
    public function getOne(array $fieldList, array $where = [])
    {
        $query = self::query()->select($fieldList);
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query->first();
    }

    /**
     * 仅仅只是为了兼容已废弃的 MySqlBase 的代码，每见到这个调用，请舍弃掉
     *
     * @deprecated
     * @param array $where
     * @return int
     */
    public function getCount(array $where = [])
    {
        $query = self::query();
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        return $query->count();
    }

    /**
     * 仅仅只是为了兼容已废弃的 MySqlBase 的代码，每见到这个调用，请舍弃掉
     *
     * @deprecated
     * @param array $fieldList
     * @param array $where
     * @param int $skip
     * @param int $limit
     * @param string[] $sorts
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBatch(array $fieldList, array $where = [], $skip = 0, $limit = 1000, $sorts = ['id' => 'asc'])
    {
        $query = self::query()->select($fieldList);
        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }
        $query->skip($skip)->limit($limit);
        foreach ($sorts as $key => $value) {
            $query->orderBy($key, $value);
        }
        return $query->get();
    }
}