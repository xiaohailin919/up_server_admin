<?php
/**
 * Created by PhpStorm.
 * User: 86135
 * Date: 2019/11/20
 * Time: 14:20
 */

namespace App\Utils;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChannelAdapter
{
    // role_id
    const CHANNEL_TAG_TOPON_ADMIN = "1";
    const CHANNEL_TAG_TOPON_OPERATION = "2";
    const CHANNEL_TAG_233 = "3"; // 渠道233

    const PAGE_PUBLISHER_INDEX = "publisher.index";
    const PAGE_PUBLISHER_EDIT = "publisher.edit";
    const PAGE_REPORT_INDEX = "report.index";

    public static function getViewFile($page, $adminId)
    {
        $roleId = self::getRole($adminId);
        $pageTemplateCfg = [
            self::PAGE_PUBLISHER_INDEX => [ // manager publisher页面
                self::CHANNEL_TAG_233 => 'publisher.index-233',
                // ... more channel in publisher.index page
            ],
            self::PAGE_PUBLISHER_EDIT => [ // edit publisher页面
                self::CHANNEL_TAG_233 => 'publisher.edit-233',
            ],
            self::PAGE_REPORT_INDEX => [ // edit publisher页面
                self::CHANNEL_TAG_233 => 'report.index-233',
            ],

            // ... more page in different channel
        ];

        return isset($pageTemplateCfg[$page][$roleId]) ? $pageTemplateCfg[$page][$roleId] : $page;
    }

    public static function getRole($adminId)
    {
        $itemList = DB::table("users as u")->leftJoin("model_has_roles as mhr", 'u.id', '=', 'mhr.model_id')->select("u.id as id", "mhr.role_id as role_id")->where("u.id", $adminId)->first();
        return $itemList['role_id'];
    }

    /**
     * 适配 manager publisher页面的查询器
     * @param $queryBuilder
     * @param $adminId
     */
    public static function adaptPublisherQueryBuilder(&$queryBuilder, $adminId)
    {
        Log::info("adaptPublisherQueryBuilder:" . $adminId);
        $roleId = self::getRole($adminId);
        switch($roleId){
            case self::CHANNEL_TAG_233:
                /* case self::CHANNEL_TAG_OTHER_CHANNEL: // more channel */
                $queryBuilder = $queryBuilder->whereBetween("id", [1000000, 2000000])
                    ->where("channel_id", 10000);
                break;
//            case self::CHANNEL_TAG_TOPON_ADMIN:
//            case self::CHANNEL_TAG_TOPON_OPERATION:
//                $queryBuilder = $queryBuilder->whereNotBetween("id", [1000000, 2000000])
//                    ->where("channel_id", 0);
//                break;
            default:
                break;
        }
    }

    /**
     * 适配 report页面的查询器
     * @param $queryBuilder
     * @param $adminId
     */
    public static function adaptReportQueryBuilder(&$queryBuilder, $adminId)
    {
        $roleId = self::getRole($adminId);
        switch($roleId){
            case self::CHANNEL_TAG_233:
                /* case self::CHANNEL_TAG_OTHER_CHANNEL: // more channel */
                $queryBuilder = $queryBuilder->whereBetween("publisher_id", [1000000, 2000000]);
                break;
            default:
                break;
        }
    }

    public static function isInChannelList($adminId)
    {
        $role = self::getRole($adminId);
        return in_array($role, [
//            self::CHANNEL_TAG_TOPON_ADMIN,
//            self::CHANNEL_TAG_TOPON_OPERATION,
            self::CHANNEL_TAG_233,
        ]);
    }
}