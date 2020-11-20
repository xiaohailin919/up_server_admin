<?php

namespace App\Models\MySql;

class StrategySDKPlugin extends Base
{
    protected $table = 'plugin_global_sdk_strategy';
    const TABLE = 'plugin_global_sdk_strategy';

    protected $guarded = ['id'];
}
