<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;


class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $adminId = 0;

    public function __construct() {
        //用户认证中间件
        $this->middleware(['auth', 'channel_host_access', 'user.status']);
        // 初始化全局变量
        $this->adminId = Auth::id();
    }

    /**
     * 是否拥有此权限
     * @param $permission
     * @return mixed
     */
    public function hasPermissionTo($permission)
    {
        return Auth::user()->hasPermissionTo($permission);
    }

    /**
     * 检查是否拥有此权限。若无则直接抛出401的异常
     * @param $permission
     */
    public function checkAccessPermission($permission)
    {
        if (!$this->hasPermissionTo($permission)) {
            abort('401');
        }
    }
}
