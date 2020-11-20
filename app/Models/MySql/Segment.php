<?php

namespace App\Models\MySql;

class Segment extends Base
{
    protected $table = 'segment';
    const TABLE = 'segment';

    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;
}