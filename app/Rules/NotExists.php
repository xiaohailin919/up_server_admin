<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class NotExists implements Rule
{
    private $table;
    private $field;
    private $exceptIdList;
    private $whereRaw;

    /**
     * 检测该字段值是否已存在数据库中，若已存在则返回 false
     * 若已配置 exceptIdList，则跳过该列表中的记录
     *
     * @param $table
     * @param string $field
     * @param array $exceptIdList 不检测的记录
     * @param string $whereRaw 额外的 where 条件
     */
    public function __construct($table, $field = "", $exceptIdList = [], $whereRaw = '')
    {
        $this->table = $table;
        $this->field = $field;
        $this->exceptIdList = $exceptIdList;
        $this->whereRaw = $whereRaw;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) : bool
    {
        $query = DB::table($this->table)->whereNotIn('id', $this->exceptIdList);
        if ($this->field == '') {
            $query->where($attribute, $value);
        } else {
            $query->where($this->field, $value);
        }
        if ($this->whereRaw != '') {
            $query->whereRaw($this->whereRaw);
        }
        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() : string
    {
        return __('validation.attributes.not_exists');
    }
}
