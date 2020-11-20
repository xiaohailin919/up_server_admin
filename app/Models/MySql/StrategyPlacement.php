<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategyPlacement extends Base
{
    const TABLE = 'strategy_placement';
    protected $table = 'strategy_placement';

    // 是否同步展示广告对象给独立插件
    const SHOW_SYNC_NO  = 1;
    const SHOW_SYNC_YES = 2;

    // 是否同步点击广告对象给独立插件
    const CLICK_SYNC_NO  = 1;
    const CLICK_SYNC_YES = 2;


    const WIFI_AUTO_PLAY_ON  = 1;
    const WIFI_AUTO_PLAY_OFF = 0;

    const SHOW_TYPE_PRIORITY = 0;
    const SHOW_TYPE_TURN     = 1;

    const REFRESH_ON  = 1;
    const REFRESH_OFF = 0;

    const STATUS_DELETED = 0;
    const STATUS_STOP    = 1;
    const STATUS_RUNNING = 2;

    protected $guarded = ['id'];

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public static function getStatusName($status)
    {
        $map = self::getStatusMap(true);
        return $map[$status];
    }
    
    /**
     * 获取所有状态码映射配置
     * @param boolean $deleted
     * @return array
     */
    public static function getStatusMap($deleted = false)
    {
        $map = [
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_STOP => 'Stop',
            self::STATUS_RUNNING => 'Running',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }

    public function getShowSyncSwitchMap()
    {
        return [
            self::SHOW_SYNC_YES => "Yes",
            self::SHOW_SYNC_NO => "No",
        ];
    }

    public function getClickSyncSwitchMap()
    {
        return [
            self::CLICK_SYNC_YES => "Yes",
            self::CLICK_SYNC_NO => "No",
        ];
    }
}