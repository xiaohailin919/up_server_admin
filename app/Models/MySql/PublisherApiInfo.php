<?php
/**
 * Created by PhpStorm.
 * User: 86135
 * Date: 2019/7/3
 * Time: 19:51
 */

namespace App\Models\MySql;

class PublisherApiInfo extends Base
{
    protected $table = 'publisher_api_info';
    protected $guarded = ['id'];


    const UP_API_PERMISSION_OFF = 1;
    const UP_API_PERMISSION_ON = 2;

    const UP_DEVICE_PERMISSION_OFF = 1;
    const UP_DEVICE_PERMISSION_ON = 2;

}