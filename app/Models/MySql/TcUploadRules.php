<?php

namespace App\Models\MySql;

use App\Models\MySqlBase;

class TcUploadRules extends MySqlBase
{
    const STATUS_PAUSED = 2;
    const STATUS_ACTIVE = 3;

    const DA_KEY_SEP = "\r\n";

    protected $table = 'tc_upload_rules';

    /**
     * 获取所有状态码映射配置
     * @return array
     */
    public function getStatusMap()
    {
        return [
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    public function getStatusMap2()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public function getStatusName($status)
    {
        $map = $this->getStatusMap();
        return $map[$status];
    }
}
