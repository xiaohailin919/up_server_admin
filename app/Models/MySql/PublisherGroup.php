<?php

namespace App\Models\MySql;

class PublisherGroup extends Base
{
    protected $table = 'publisher_group';

    const TYPE_SDK_DISTRIBUTION = 1;

    private static $idNameMap;

    public static function getName($id) {
        self::$idNameMap = self::getPublisherGroupIdNameMap();
        return self::$idNameMap[$id] ?? '';
    }

    public static function getPublisherGroupIdNameMap() {
        if (self::$idNameMap == null) {
            $data = self::query()->get(['id', 'name'])->toArray();
            self::$idNameMap = array_column($data, 'name', 'id');
        }
        return self::$idNameMap;
    }
}
