<?php

namespace App\Models\MySql;

class AdxDemand extends Base
{
    protected $table = 'adx_demand';

    // 类型
    const TYPE_ADX = 1;
    const TYPE_DSP = 2;

    // 结算类型
    const BILLING_TYPE_FIRST_PRICE  = 1;
    const BILLING_TYPE_SECOND_PRICE = 2;

    // 结算币种
    const BILLING_CURRENCY_CNY = 'CNY';
    const BILLING_CURRENCY_USD = 'USD';

    // 计费依据
    const BILLING_BASIS_NURL       = 1;
    const BILLING_BASIS_IMPRESSION = 2;

    // 状态
    const STATUS_STOP    = 1;
    const STATUS_ACTIVE  = 3;
    const STATUS_DOCKING = 4;

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['create_time', 'id'];

    /**
     * 获取类型Map
     *
     * @return string[]
     */
    public static function getTypeMap(): array{
        return [
            self::TYPE_ADX => 'ADX',
            self::TYPE_DSP => 'DSP',
        ];
    }

    /**
     * 获取结算类型Map
     *
     * @return array|string[]
     */
    public static function getBillingTypeMap(): array{
        return [
            self::BILLING_TYPE_FIRST_PRICE  => 'First Price',
            self::BILLING_TYPE_SECOND_PRICE => 'Second Price',
        ];
    }

    /**
     * 获取结算币种Map
     *
     * @return array|string[]
     */
    public static function getBillingCurrencyMap(): array{
        return [
            self::BILLING_CURRENCY_CNY => 'CNY',
            self::BILLING_CURRENCY_USD => 'USD',
        ];
    }

    /**
     * 获取计费依据Map
     *
     * @return array|string[]
     */
    public static function getBillingBasisMap(): array{
        return [
            self::BILLING_BASIS_NURL       => 'NURL',
            self::BILLING_BASIS_IMPRESSION => 'Impression',
        ];
    }

    /**
     * 获取状态Map
     *
     * @return array|string[]
     */
    public static function getStatusMap(): array{
        return [
            self::STATUS_STOP    => 'Stop',
            self::STATUS_ACTIVE  => 'Active',
            self::STATUS_DOCKING => 'Docking'
        ];
    }
}
