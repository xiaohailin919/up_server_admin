<?php

namespace App\Models\Mongo;

use App\Models\MongoBase;

class App extends MongoBase
{
    protected $table = 'app';

    const STATUS_RUNNING = 1;

    const STATUS_STOP = 0;
}

