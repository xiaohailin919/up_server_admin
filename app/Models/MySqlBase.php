<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\MySql\Base;

/**
 * 注：此基类已废弃，历史代码有发现使用该类的，请改成 App\Models\MySql\Base
 * Class MySqlBase
 * @deprecated
 * @package App\Models
 */
class MySqlBase extends Base
{
    const CONNECTION = 'mysql';

    const SYSTEM_UP = 1;
    const SYSTEM_AM = 2;
    const PLATFORM_ANDROID = 1;
    const PLATFORM_IOS = 2;
    const TYPE_WHITE = 1;
    const TYPE_BLACK = 2;

    protected $table = '';

    public function getTable()
    {
        return $this->table;
    }

    public function getConnection()
    {
        return DB::connection(self::CONNECTION);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function queryBuilder()
    {
        return $query = self::getConnection()
            ->table($this->table);
    }

    /**
     * @deprecated
     * @param array $fieldList
     * @param array $where
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    private function queryBuilderSp(array $fieldList, array $where = [])
    {
        $query = $this->queryBuilder();

        foreach($fieldList as $num=>$field){
            $query->addSelect($field);
        }

        if(!empty($where)){
            foreach($where as $k => $v){
                $query->where($k, $v);
            }
        }

        return $query;
    }

    /**
     * 获取满足条件的记录
     * @param array $fieldList
     * @param array $where
     * @return mixed
     * @deprecated
     */
    public function get(array $fieldList, array $where = [])
    {
        $query = $this->queryBuilderSp($fieldList, $where);
        return $query->get();
    }

    /**
     * 获取满足条件的第一条记录
     * @param array $fieldList
     * @param array $where
     * @return mixed
     * @deprecated
     */
    public function getOne(array $fieldList, array $where = [])
    {
        $query = $this->queryBuilderSp($fieldList, $where);
        return $query->first();
    }

    /**
     * 获取总数
     * @deprecated
     */
    public function getCount(array $where = [])
    {
        $query = $this->queryBuilder();
        if (!empty($where)) {
            foreach($where as $k => $v) {
                $query->where($k, $v);
            }
        }
        return $query->count();
    }

    /**
     * 获取分页总数
     * @deprecated
     */
    public function getBatch(array $fieldList, array $where = [], $skip = 0, $limit = 1000, $sorts = ['id' => 'asc'])
    {
        $query = $this->queryBuilderSp($fieldList, $where)->skip($skip)->limit($limit);
        if (!empty($sorts)) {
            foreach ($sorts as $k => $v) {
                $query->orderBy($k, $v);
            }
        }
        return $query->get();
    }
}

