<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2018/8/30
 * Time: 14:42
 */
namespace App\Models\MySql;

use App\Models\MySqlBase;

class StrategyAppFirm extends MySqlBase
{
    const STATUS_DELETED = 0;
    const STATUS_STOP = 1;
    const STATUS_RUNNING = 2;

    protected $table = 'strategy_app_firm';

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
            self::STATUS_STOP => 'Stop',
            self::STATUS_RUNNING => 'Running',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }
}