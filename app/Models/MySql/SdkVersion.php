<?php


namespace App\Models\MySql;


use App\Helpers\Format;
use DateTime;

class SdkVersion extends Base
{
    protected $table = 'sdk_version';
    public $timestamps = true;

    const TYPE_NW_FIRM = 0;
    const TYPE_AND = 1;
    const TYPE_IOS = 2;
    const TYPE_UNITY_AND = 11;
    const TYPE_UNITY_IOS = 12;
    const TYPE_UNITY_AND_IOS = 13;

    const AREA_NATIVE = 1;
    const AREA_FOREIGN = 9999;

    const STATUS_ACTIVE = 3;
    const STATUS_CLOSED = 1;

    protected $guarded = ['id'];

    private static $typeVersionMap;

    /**
     * 数据库时间格式转换
     *
     * @param DateTime|int $value 从数据库读取则为 Timestamp 类型，框架写入则为 int 类型
     * @return DateTime|false|int|string
     */
    public function fromDateTime($value) {
        return is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 判断该 SDK 版本是否任生效
     *
     * @param $version
     * @param $type
     * @return bool
     */
    public static function isActive($version, $type): bool
    {
        if (self::$typeVersionMap == null) {
            $data = self::query()
                ->where('status', self::STATUS_ACTIVE)
                ->where('type', '!=', self::TYPE_NW_FIRM)
                ->get(['type', 'version'])->toArray();

            self::$typeVersionMap = [
                self::TYPE_AND => [],
                self::TYPE_IOS => [],
                self::TYPE_UNITY_AND => [],
                self::TYPE_UNITY_IOS => [],
                self::TYPE_UNITY_AND_IOS => [],
            ];

            foreach ($data as $datum) {
                self::$typeVersionMap[$datum['type']][] = $datum['version'];
            }
        }
        return array_key_exists($type, self::$typeVersionMap) ? in_array($version, self::$typeVersionMap[$type], true) : false;
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_NW_FIRM => 'Network Firm',
            self::TYPE_AND => 'Android',
            self::TYPE_IOS => 'iOS',
            self::TYPE_UNITY_AND => 'Unity Android',
            self::TYPE_UNITY_IOS => 'Unity iOS',
            self::TYPE_UNITY_AND_IOS => 'Unity Android & iOS',
        ];
    }

    public static function getAreaMap(): array {
        return [
            self::AREA_NATIVE => 'cn',
            self::AREA_FOREIGN => 'other',
        ];
    }

    public static function getAreaName($value): string
    {
        $areaMap = self::getAreaMap();
        return $areaMap[$value] ?? '-';
    }

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_CLOSED => 'closed',
        ];
    }

}