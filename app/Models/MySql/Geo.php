<?php

namespace App\Models\MySql;

class Geo extends Base
{
    protected $table = 'geo';
    const TABLE = 'geo';


    public static function getGeoMap()
    {
        $geo = self::query()->get()->toArray();
        return array_column($geo, 'name', 'short');
    }
}