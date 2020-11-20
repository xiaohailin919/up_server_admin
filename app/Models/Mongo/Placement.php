<?php

namespace App\Models\Mongo;

use App\Models\MongoBase;

class Placement extends MongoBase
{
    protected $table = 'placement';

    const STATUS_RUNNING = 1;

    const STATUS_STOP = 0;
}

