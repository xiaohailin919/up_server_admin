<?php

namespace App\Models\MySql;

use App\Helpers\Format;

class DeductionRule extends Base {

    protected $table = 'deduction_rule';
    const TABLE = 'deduction_rule';

    protected $guarded = ['id'];

    const DISCOUNT_IMPRESSION_DEFAULT_VALUE = 95;
    const DISCOUNT_FILL_RATE_DEFAULT_VALUE = 110;
    const DISCOUNT_MIN_VALUE = 0;

    const TYPE_DEFAULT      = 0;
    const TYPE_IMPRESSION   = 1;
    const TYPE_FILLED_RATE  = 2;

    const STATUS_ACTIVE = 3;
    const STATUS_TO_CHECK = 2;
    const STATUS_PAUSED = 1;

    const DIMENSION_PUBLISHER = 1;
    const DIMENSION_APP = 2;
    const DIMENSION_PLACEMENT = 3;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public static function getStatusMap(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAUSED => 'Paused',
//            self::STATUS_TO_CHECK => 'To check',
        ];
    }

    public static function getDimensionMap(): array
    {
        return [
            self::DIMENSION_PUBLISHER => 'Publisher',
            self::DIMENSION_APP       => 'App',
            self::DIMENSION_PLACEMENT => 'Placement',
        ];
    }

    public static function getTypeMap(): array
    {
        return [
            self::TYPE_IMPRESSION => 'Impression',
            self::TYPE_FILLED_RATE => 'Filled rate'
        ];
    }
}
