<?php

namespace App\Models\Mongo;

use App\Models\MongoBase;

class Publisher extends MongoBase
{
    protected $table = 'publisher';

    const STATUS_RUNNING = 1;

    const STATUS_STOP = 0;
}

