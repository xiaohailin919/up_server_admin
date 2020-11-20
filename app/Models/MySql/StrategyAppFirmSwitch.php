<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/8/30
 * Time: 14:42
 */
namespace App\Models\MySql;

use App\Models\MySqlBase;

class StrategyAppFirmSwitch extends MySqlBase
{
    const STATUS_DELETED = 0;
    const STATUS_PAUSED = 2;
    const STATUS_ACTIVE = 3;

    const PKG_LIST_SEP = "\r\n";

    protected $table = 'strategy_app_firm_switch';

    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public function getStatusName($status)
    {
        $map = $this->getStatusMap(true);
        return $map[$status];
    }

    /**
     * 获取所有状态码映射配置
     * @param boolean $deleted
     * @return array
     */
    public function getStatusMap($deleted = false)
    {
        $map = [
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_PAUSED => 'Off',
            self::STATUS_ACTIVE => 'On',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }
}