<?php
/**
 * Created by PhpStorm.
 * User: 86135
 * Date: 2019/1/30
 * Time: 18:28
 */

namespace App\Models\MySql;


use App\Helpers\Format;

class ReportUnitLog extends Base
{
    protected $table = 'report_unit_log';
    const TABLE = 'report_unit_log';

    const STATUS_FAILED  = 0;
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 3;

    const PULL_TYPE_DAY = 1;
    const PULL_TYPE_HOUR = 2;

    const TYPE_MANUAL    = 1;
    const TYPE_AUTO      = 2;

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getPullStartTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getPullEndTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_PENDING => '等待',
            self::STATUS_FAILED  => '失败',
            self::STATUS_SUCCESS => '成功',
        ];
    }

    public static function getStatusName($status): string
    {
        $map = self::getStatusMap();
        return $map[$status] ?? '-';
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_MANUAL => '手动',
            self::TYPE_AUTO   => '自动'
        ];
    }

    public static function getTypeName($type): string
    {
        $map = self::getTypeMap();
        return $map[(int)$type] ?? '-';
    }

    public static function getPullTypeMap(): array
    {
        return [
            self::PULL_TYPE_DAY => '天维度',
            self::PULL_TYPE_HOUR => '小时维度',
        ];
    }

    public static function getPullTypeName($pullType): string
    {
        $map = self::getPullTypeMap();
        return $map[(int)$pullType] ?? '-';
    }


    public static function getNwFirmMap()
    {
        $map = parent::getNwFirmMap();
        $map[0] = '全部平台';
        ksort($map);
        return $map;
    }
}