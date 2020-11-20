<?php

namespace App\Models\MySql;

class AdxStrategy extends Base
{
    protected $table = 'adx_strategy';

    const TABLE = 'adx_strategy';

    // 维度
    const DIMENSION_FORMAT    = 1;
    const DIMENSION_PLACEMENT = 2;

    // 点击视频可跳转
    const VIDEO_CLICKABLE_NO  = 1;
    const VIDEO_CLICKABLE_YES = 2;

    // EndCard点击区域控制
    const END_CARD_CLICK_AREA_FULLSCREEN = 1;
    const END_CARD_CLICK_AREA_CTA        = 2;
    const END_CARD_CLICK_AREA_BANNER     = 3;

    // click url的打开模式
    const CLICK_MODE_SYNC  = 1;
    const CLICK_MODE_ASYNC = 2;

    // click url跳转失败后的打开方式
    const LOAD_TYPE_BROWSER = 1;
    const LOAD_TYPE_WEBVIEW = 2;

    // 展示上报UA时机
    const IMPRESSION_UA_WEBVIEW = 1;
    const IMPRESSION_UA_SYSTEM  = 2;

    // 点击上报UA时机
    const CLICK_UA_WEBVIEW = 1;
    const CLICK_UA_SYSTEM  = 2;

    // Android国内版本APK下载二次确认
    const APK_DOWNLOAD_CONFIRM_OFF = 1;
    const APK_DOWNLOAD_CONFIRM_ON  = 2;

    // StoreKit加载时机
    const STORE_KIT_TIME_EARLY_LOADING     = 1;
    const STORE_KIT_TIME_REAL_TIME_LOADING = 2;
    const STORE_KIT_DO_NOT_USE             = 3;

    // deeplink 跳转时的 Click URL 点击时机
    const DP_CM_NOT = 1;
    const DP_CM_PRE = 2;
    const DP_CM_AFTER = 3;

    // 状态
    const STATUS_STOP   = 1;
    const STATUS_ACTIVE = 3;

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['create_time', 'id'];


    /**
     * 点击视频可跳转 map
     *
     * @return string[]
     */
    public static function getVideoClickableMap(){
        return [
            self::VIDEO_CLICKABLE_NO  => 'No',
            self::VIDEO_CLICKABLE_YES => 'Yes',
        ];
    }

    /**
     * EndCard点击区域控制
     *
     * @return string[]
     */
    public static function getEndCardClickAreaMap(){
        return [
            self::END_CARD_CLICK_AREA_FULLSCREEN => '全屏',
            self::END_CARD_CLICK_AREA_CTA        => 'CTA',
            self::END_CARD_CLICK_AREA_BANNER     => 'Banner区域',
        ];
    }

    /**
     * click url的打开模式
     *
     * @return string[]
     */
    public static function getClickModeMap(){
        return [
            self::CLICK_MODE_SYNC  => '同步点击',
            self::CLICK_MODE_ASYNC => '异步点击',
        ];
    }

    /**
     * click url跳转失败后的打开方式
     *
     * @return string[]
     */
    public static function getLoadTypeMap(){
        return [
            self::LOAD_TYPE_BROWSER => '浏览器',
            self::LOAD_TYPE_WEBVIEW => 'SDK内置webview',
        ];
    }

    /**
     * 展示上报UA时机
     *
     * @return string[]
     */
    public static function getImpressionUaMap(){
        return [
            self::IMPRESSION_UA_WEBVIEW => 'Webview UA',
            self::IMPRESSION_UA_SYSTEM  => 'System UA',
        ];
    }

    /**
     * 点击上报UA时机
     *
     * @return string[]
     */
    public static function getClickUaMap(){
        return [
            self::CLICK_UA_WEBVIEW => 'Webview UA',
            self::CLICK_UA_SYSTEM  => 'System UA',
        ];
    }

    /**
     * Android国内版本APK下载二次确认
     *
     * @return string[]
     */
    public static function getApkDownloadConfirmMap(){
        return [
            self::APK_DOWNLOAD_CONFIRM_OFF => '不需要',
            self::APK_DOWNLOAD_CONFIRM_ON  => '需要',
        ];
    }

    /**
     * StoreKit加载时机
     *
     * @return string[]
     */
    public static function getStoreKitTimeMap(){
        return [
            self::STORE_KIT_TIME_EARLY_LOADING      => '提前加载',
            self::STORE_KIT_TIME_REAL_TIME_LOADING  => '实时加载',
            self::STORE_KIT_DO_NOT_USE              => '不使用StoreKit',
        ];
    }

    /**
     * deeplink 跳转时的 Click URL 点击时机
     *
     * @return string[]
     */
    public static function getDpCmMap() {
        return [
            self::DP_CM_NOT => '不点击',
            self::DP_CM_PRE => '跳转前点击',
            self::DP_CM_AFTER => '跳转后点击'
        ];
    }

    /**
     * 获取状态Map
     *
     * @return array|string[]
     */
    public static function getStatusMap(): array{
        return [
            self::STATUS_STOP   => 'Stop',
            self::STATUS_ACTIVE => 'Active',
        ];
    }
}
