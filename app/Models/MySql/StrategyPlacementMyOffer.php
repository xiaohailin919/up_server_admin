<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategyPlacementMyOffer extends Base
{
    protected $table = 'strategy_placement_myoffer';
    const TABLE = 'strategy_placement_myoffer';

    protected $guarded = ['id'];

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    const STATUS_DELETED = 0;
    const STATUS_PAUSED = 1;
    const STATUS_RUNNING = 2;

    // 点击视频可跳转
    const VIDEO_CLICKABLE_YES = 2;
    const VIDEO_CLICKABLE_NO = 1;

    // EndCard点击区域控制
    const END_CARD_CLICK_AREA_FULL_SCREEN = 0;
    const END_CARD_CLICK_AREA_CTA = 1;
    const END_CARD_CLICK_AREA_BANNER = 2;

    //采用静音模式
    const VIDEO_MUTE_YES = 2;
    const VIDEO_MUTE_NO = 1;

    const OFFER_CACHE_TIME = 604800;// 秒

    // Android国内版本APK下载二次确认
    const APK_DOWNLOAD_CONFIRM_OFF = 1;
    const APK_DOWNLOAD_CONFIRM_ON  = 2;

    // StoreKit加载时机
    const STORE_KIT_TIME_EARLY_LOADING     = 1;
    const STORE_KIT_TIME_REAL_TIME_LOADING = 2;
    const STORE_KIT_DO_NOT_USE             = 3;
}
