<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class Placement extends MySqlBase
{
    protected $table = 'placement';

    const TABLE = 'placement';
    const INDEX_ID_UUID = 'idx_id_uuid';

    protected $fillable = [
        'status', 'update_time'
    ];

    const STATUS_DELETED = 0;
    const STATUS_LOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;

    const PRIVATE_STATUS_DELETED = 0;
    const PRIVATE_STATUS_LOCKED = 1;
    const PRIVATE_STATUS_PENDING = 2;
    const PRIVATE_STATUS_RUNNING = 3;

    private static $statusMap;
    private static $idUuidMap;
    private static $idNameMap;

    public static function getStatusMap($delete = false): array {
        $map = [
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_LOCKED  => 'Locked',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RUNNING => 'Running',
        ];
        if (!$delete) {
            unset($map[self::STATUS_DELETE]);
        }
        return $map;
    }

    public static function getStatusName($status) {
        if (self::$statusMap == null) {
            self::$statusMap = self::getStatusMap(true);
        }
        return self::$statusMap[$status] ?? '';
    }

    public static function getIdUuidMap()
    {
        $data = self::query()->select(['id', 'uuid'])->get()->toArray();
        return array_column($data, 'uuid', 'id');
    }
    
    /**
     * 通过主键ID获取UUID
     *
     * @param $id
     * @return string
     */
    public static function getUuid($id)
    {
        if (self::$idUuidMap == null) {
            self::$idUuidMap = self::getIdUuidMap();
        }
        return self::$idUuidMap[$id] ?? '';
    }

    public static function getIdNameMap()
    {
        $data = self::query()->select(['id', 'name'])->get()->toArray();
        return array_column($data, 'name', 'id');
    }

    /**
     * 通过主键 ID 获取广告位名称
     *
     * @param $id
     * @return mixed|string
     */
    public static function getName($id)
    {
        if (self::$idNameMap == null) {
            self::$idNameMap = self::getIdNameMap();
        }
        return self::$idNameMap[$id] ?? '';
    }

    /**
     * 根据app id修改 private status
     *
     * @deprecated
     * @param $appId
     * @param $status
     */
    public function updatePrivateStatusByAppId($appId, $status)
    {
        self::query()->where('app_id', $appId)->update([
            'private_status' => $status,
            'update_time' => time(),
        ]);
    }

    /**
     * 获取private状态码映射的名称
     *
     * @deprecated
     * @param $status
     * @return string
     */
    public function getPrivateStatusName($status)
    {
        return self::getStatusName($status);
    }

    /**
     * 获取所有private状态码映射配置
     *
     * @deprecated
     * @param boolean $deleted
     * @return array
     */
    public function getPrivateStatusMap($deleted = false)
    {
        return self::getStatusMap($deleted);
    }
}