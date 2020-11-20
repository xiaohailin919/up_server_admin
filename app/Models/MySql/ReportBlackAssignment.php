<?php
/**
 * Created by PhpStorm.
 * User: SA
 * Date: 2019/1/22
 * Time: 19:54
 */

namespace App\Models\MySql;

use App\Helpers\Format;

class ReportBlackAssignment extends Base
{
    protected $table = 'report_black_assignment';
    const TABLE = 'report_black_assignment';

    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'utime';

    const TYPE_MAIN = 1;
    const TYPE_SUB = 2;

    const TYPE_ASSIGNMENT_DISCOUNT = 1;
    const TYPE_ASSIGNMENT_ECPM = 2;

    const STATUS_DELETED = 0;
    const STATUS_LOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;

    protected $guarded = ['id'];

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
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
            self::STATUS_LOCKED => 'Paused',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RUNNING => 'Running',
        ];
        if (!$deleted) {
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }

    public static function getAssignmentTypeMap(): array
    {
        return [
            self::TYPE_ASSIGNMENT_DISCOUNT => 'Discount',
            self::TYPE_ASSIGNMENT_ECPM => 'eCPM',
        ];
    }

    public function getDiscountActualValue($oneRevenueDeduction, $random = 10000)
    {
        $expectedValue = $oneRevenueDeduction["expected_value"] / 100;
        if ($random != 10000) {
            $randomZ = $random;
        }else{
            $range = $oneRevenueDeduction['random_range'];
            $randomZ = rand(-$range, $range);
        }
        $randomPercent = $randomZ /100;
        $lastX = $expectedValue * (1 + $randomPercent);
        $res = ['random' => $randomZ, 'last' => $lastX];
        return $res;
    }

    public function getEcpmActualValue($oneRevenueDeduction, $random = 10000)
    {
        $expectedValue = $oneRevenueDeduction["expected_value"] / 1000;
        if ($random != 10000) {
            $randomZ = $random;
        }else{
            $range = $oneRevenueDeduction['random_range'];
            $randomZ = rand(-$range, $range);
        }
        $randomPercent = $randomZ /100;
        $lastX = $expectedValue * (1 + $randomPercent);
        $res = ['random' => $randomZ, 'last' => $lastX];
        return $res;
    }


}