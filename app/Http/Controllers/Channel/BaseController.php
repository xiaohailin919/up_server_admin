<?php

namespace App\Http\Controllers\Channel;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BsController;

use App\Helpers\Channel;

class BaseController extends BsController
{

    protected $roleId    = 0;
    protected $channelId = 0;

    function __construct()
    {
        parent::__construct();
//        // 初始化全局变量
//        $this->roleId    = Channel::getRoleId($this->adminId);
//        $this->channelId = Channel::getChannelId($this->roleId);
    }
}
