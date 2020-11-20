<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategyPlacementFirm extends Base
{
    protected $table = 'strategy_placement_firm';
    const TABLE = 'strategy_placement_firm';

    protected $guarded = ['id'];

    const STATUS_DELETED = 0;
    const STATUS_STOP = 1;
    const STATUS_RUNNING = 2;


    // 是否同步展示广告对象给独立插件
    const SHOW_SYNC_NO = 1;
    const SHOW_SYNC_YES = 2;

    // 是否同步点击广告对象给独立插件
    const CLICK_SYNC_NO = 1;
    const CLICK_SYNC_YES = 2;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }
    
    public function getStrategyByStrPlId($strPlId)
    {
        if($strPlId <= 0){
            return [];
        }
        $field = [
            'nw_firm_id',
            'nw_cache_time',
            'nw_timeout',
            'nw_offer_requests'
        ];
        $strategy = self::query()->where('str_placement_id', $strPlId)->where('status', self::STATUS_RUNNING)
            ->get($field);
        $firm = [];
        foreach($strategy as $val){
            $firm[$val['nw_firm_id']] = $val;
        }
        return $firm;
    }
}