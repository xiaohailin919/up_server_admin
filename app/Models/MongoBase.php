<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class MongoBase
{
    const CONNECTION = 'mongodb';

    protected $table = '';

    public function getTable()
    {
        return $this->table;
    }

    protected static function getConnection()
    {
        return DB::connection(self::CONNECTION);
    }

    public function queryBuilder()
    {
        return $query = self::getConnection()
            ->table($this->table);
    }

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
     */
    public function getOne(array $fieldList, array $where = [])
    {
        $query = $this->queryBuilderSp($fieldList, $where);
        return $query->first();
    }
}

