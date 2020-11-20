<?php

namespace App\Models\Mongo;

use Jenssegers\Mongodb\Eloquent\Model;

class AdInsightsAppRank extends Model
{
    protected $connection = 'mongodb-2';

    protected $table = 'ad_insights_app_rank';

    const PLATFORM_ANDROID = 1;
    const PLATFORM_IOS = 2;

    const DATA_RANGE_ALL = 1;
    const DATA_RANGE_NEW = 2;
}
