<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class StrategyApp extends Base
{
    const TABLE = 'strategy_app';
    protected $table = 'strategy_app';

    const CRASH_SWITCH_ON  = 2;
    const CRASH_SWITCH_OFF = 1;

    const STATUS_DELETED = 0;
    const STATUS_STOP = 1;
    const STATUS_OPEN = 2;

    const GDPR_SWITCH_ON = 2;
    const GDPR_SWITCH_OFF = 1;

    const LEAVE_APP_SWITCH_ON = 2;
    const LEAVE_APP_SWITCH_OFF = 1;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    private $defaultStrategy = [
        'cache_time' => 3600,
        'placement_timeout' => 5,
        'new_psid_time' => 30,
        'cache_areasize' => 50,
        'collect_material' => 0,
        'collect_app_list' => 0,
        'rf_install' => 0,
        'rf_download' => 0,
        'rf_key' => '',
        'rf_app_id' => 0,
        'rf_power' => 0,
        'rf_power2' => 0,
        'gdpr_consent_set' => 0,
        'gdpr_server_only' => 0,
        'gdpr_notify_url' => '',
        'notice_list' => '',
        'network_pre_init_list' => '[]',
        'network_gdpr_switch' => self::GDPR_SWITCH_ON,
        'data_level' => '{"m":1,"i":1,"a":1}',
        'psid_hot_leave' => 60,
        'leave_app_switch' => self::LEAVE_APP_SWITCH_ON,
        'crash_sw' => self::CRASH_SWITCH_ON,
        'crash_list' => '[]',
    ];

    public static $androidInitAdapter = [
        6  => 'com.anythink.network.mintegral.MintegralATInitManager',
        15 => 'com.anythink.network.toutiao.TTATInitManager',
    ];

    public function getStrategy($appId)
    {
        if($appId <= 0){
            return $this->defaultStrategy;
        }
        $field = [
            'cache_time',
            'placement_timeout',
            'new_psid_time',
            'cache_areasize',
            'collect_material',
            'collect_app_list',
            'rf_install',
            'rf_download',
            'rf_key',
            'rf_app_id',
            'rf_power',
            'rf_power2',
            'gdpr_consent_set',
            'gdpr_server_only',
            'gdpr_notify_url',
            'notice_list',
            'network_pre_init_list',
            'network_gdpr_switch',
            'data_level',
            'psid_hot_leave',
            'leave_app_switch',
            'crash_sw',
            'crash_list',
            "req_sw",
            "req_addr",
            "req_tcp_addr",
            "req_tcp_port",
            "bid_sw",
            "bid_addr",
            "tk_sw",
            "tk_addr",
            "tk_tcp_addr",
            "tk_tcp_port",
            "adx_apk_sw",
            "ol_sw",
            "ol_req_addr",
            "ol_tcp_addr",
            "ol_tcp_port",
        ];
        $strategy = self::query()->select($field)->where('app_id', $appId)->where('status', self::STATUS_OPEN)->first();
        if($strategy === null){
            return $this->defaultStrategy;
        }
        return $strategy;
    }
    
    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public static function getStatusName($status)
    {
        $map = self::getStatusMap(true);
        return isset($map[$status]) ? $map[$status] : '';
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
            self::STATUS_OPEN => 'Running',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }
}