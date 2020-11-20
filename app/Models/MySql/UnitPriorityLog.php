<?php
/**
 * Created by PhpStorm.
 * User: zengzhihai
 * Date: 2019/9/4
 * Time: 11:13
 */

namespace App\Models\MySql;

use App\Helpers\Format;

class UnitPriorityLog extends Base
{
    protected $table = 'unit_priority_log';
    const TABLE = 'unit_priority_log';

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }
}