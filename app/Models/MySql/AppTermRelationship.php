<?php

namespace App\Models\MySql;

use DateTime;

class AppTermRelationship extends Base
{

    protected $table = 'app_term_relationship';
    const TABLE = 'app_term_relationship';

    protected $guarded = ['id'];

    /**
     * @var bool 是否启用默认时间戳
     */
//    public $timestamps = true;

    /**
     * 数据库时间格式
     *
     * @param DateTime|int $value
     * @return DateTime|false|int|string
     */
    public function fromDateTime($value) {
        return date('Y-m-d H:i:s', $value);
    }

}