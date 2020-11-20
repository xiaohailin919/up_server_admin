<?php

namespace App\Helpers;
use App\Models\MySql\DataSortMetrics;
use DB;

class Database
{
    /**
     * 使用数据字段来获取数据表最近更新时间，性能最快。
     *
     * @param string $table
     * @return mixed|null
     */
    public static function getTableUpdateTime(string $table)
    {
        return DB::query()->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_SCHEMA', env('DB_DATABASE'))
            ->where('TABLE_NAME', $table)
            ->value('update_time');
    }
}
