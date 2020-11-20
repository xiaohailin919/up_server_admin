<?php

namespace App\Models\MySql;

class Users extends Base
{
    protected $table = 'users';
    const TABLE = 'users';

    const STATUS_RUNNING = 3;
    const STATUS_DELETED = 99;

    private static $idNameMap;

    public static function getName($id)
    {
        if (self::$idNameMap == null) {
            $data = self::query()->get(['id', 'name'])->toArray();
            self::$idNameMap = array_column($data, 'name', 'id');
        }
        return self::$idNameMap[$id] ?? 'unknown';
    }
}