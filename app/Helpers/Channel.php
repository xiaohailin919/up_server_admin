<?php
/**
 * 渠道 Helper
 */

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\MySql\Publisher;

class Channel
{
    // role_id
    const CHANNEL_TOPON_ADMIN     = '1'; // TopOn Admin
    const CHANNEL_TOPON_OPERATION = '2'; // TopOn Operation
    const CHANNEL_233             = '3'; // 渠道 233
    const CHANNEL_DLADS           = '4'; // 渠道 上海典鹿

    /**
     * 获取角色ID
     *
     * @param  int $adminId
     * @return mixed
     */
    public static function getRoleId($adminId)
    {
        if($adminId <= 0){
            $adminId = Auth::id();
        }

        $itemList = DB::table("users as u")
            ->leftJoin("model_has_roles as mhr", 'u.id', '=', 'mhr.model_id')
            ->select("u.id as id", "mhr.role_id as role_id")
            ->where("u.id", $adminId)
            ->first();

        return $itemList['role_id'];
    }

    /**
     * 通过角色ID 获取 Channel ID
     *
     * @param  int $roleId
     * @return int
     */
    public static function getChannelId($roleId){
        $map = [
            '3' => Publisher::CHANNEL_233,
            '4' => Publisher::CHANNEL_DLADS,
        ];

        return $map[$roleId];
    }
}