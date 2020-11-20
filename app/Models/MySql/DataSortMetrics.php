<?php

namespace App\Models\MySql;

class DataSortMetrics extends Base {

    protected $table = 'data_sort_metrics';
    const TABLE = 'data_sort_metrics';

    const TYPE_APP = 1;
    const TYPE_PLACEMENT = 2;

    /**
     * 一天前
     */
    const PERIOD_TYPE_DAY_ONE_DAY_AGO = 1;
    /**
     * 两天前
     */
    const PERIOD_TYPE_DAY_TWO_DAY_AGO = 2;
    /**
     * 三天前
     */
    const PERIOD_TYPE_DAY_THREE_DAY_AGO = 3;
    /**
     * 一天前一周
     */
    const PERIOD_TYPE_WEEK_ONE_DAY_AGO = 4;
    /**
     * 两天前一周
     */
    const PERIOD_TYPE_WEEK_TWO_DAY_AGO = 5;
    /**
     * 三天前一周
     */
    const PERIOD_TYPE_WEEK_THREE_DAY_AGO = 6;
    /**
     * 一天前一月
     */
    const PERIOD_TYPE_MONTH_ONE_DAY_AGO = 7;
    /**
     * 两天前一月
     */
    const PERIOD_TYPE_MONTH_TWO_DAY_AGO = 8;
    /**
     * 三天前一月
     */
    const PERIOD_TYPE_MONTH_THREE_DAY_AGO = 9;

    public static function getPeriodTypeMap(): array {
        return [
            self::PERIOD_TYPE_DAY_ONE_DAY_AGO         => __('common.data_sort_metrics_one_day_ago'),
            self::PERIOD_TYPE_DAY_TWO_DAY_AGO         => __('common.data_sort_metrics_two_day_ago'),
            self::PERIOD_TYPE_DAY_THREE_DAY_AGO       => __('common.data_sort_metrics_three_day_ago'),
            self::PERIOD_TYPE_WEEK_ONE_DAY_AGO    => __('common.data_sort_metrics_one_day_week_ago'),
            self::PERIOD_TYPE_WEEK_TWO_DAY_AGO    => __('common.data_sort_metrics_two_day_week_ago'),
            self::PERIOD_TYPE_WEEK_THREE_DAY_AGO  => __('common.data_sort_metrics_three_day_week_ago'),
            self::PERIOD_TYPE_MONTH_ONE_DAY_AGO   => __('common.data_sort_metrics_one_day_month_ago'),
            self::PERIOD_TYPE_MONTH_TWO_DAY_AGO   => __('common.data_sort_metrics_two_day_month_ago'),
            self::PERIOD_TYPE_MONTH_THREE_DAY_AGO => __('common.data_sort_metrics_three_day_month_ago'),
        ];
    }
}
