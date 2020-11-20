<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/8/22
 * Time: 15:24
 */
namespace App\Models\MySql;

class FirmAdapter extends Base
{
    protected $table = 'firm_adapter';
    const TABLE = 'firm_adapter';

    protected $guarded = ['id'];

    private static $idNameMap;

    private static $complicatedNameMap;

    public static function getName($id) {
        if (self::$idNameMap == null) {
            $data = self::query()->get(['id', 'name'])->toArray();
            self::$idNameMap = array_column($data, 'name', 'id');
        }
        return self::$idNameMap[$id] ?? '';
    }

    /**
     * @param $nwFirmId
     * @param $format
     * @param $platform
     * @return mixed|string
     */
    public static function getNameByComplicated($nwFirmId, $format, $platform)
    {
        if (self::$complicatedNameMap == null) {
            self::$complicatedNameMap = self::getComplicatedNameMap();
        }
        return self::$complicatedNameMap[$nwFirmId][$format][$platform] ?? '';
    }

    /**
     * 获取一个由 NwFirmID、Format、Platform 嵌套的 adapter Name 列表
     * [
     *     'firm_id' => [
     *         'format' => ['android' => 'com.toponad.androidAdapter', 'ios' => ''],
     *     ],
     * ]
     */
    private static function getComplicatedNameMap(): array
    {
        $res = [];
        /* 获取所有厂商，所有 format，所有 platform */
        $allNwFirmMap = self::getAllNwFirmMap();
        $allFormatMap = self::getFormatMap();
        $allPlatformMap = self::getPlatformMap();
        $data = self::query()->get(['id', 'firm_id', 'format', 'platform', 'adapter'])->toArray();
        foreach ($allNwFirmMap as $i => $nwFirmName) {
            foreach ($allFormatMap as $j => $formatName) {
                foreach ($allPlatformMap as $k => $platformName) {
                    $res[$i][$j][$k] = '';
                }
            }
            $currentFirmAdapters = array_where($data, static function ($value) use ($i) {
                return $value['firm_id'] == $i;
            });
            foreach ($currentFirmAdapters as $adapter) {
                $res[$i][$adapter['format']][$adapter['platform']] = $adapter['adapter'];
            }
        }
        return $res;
    }
}