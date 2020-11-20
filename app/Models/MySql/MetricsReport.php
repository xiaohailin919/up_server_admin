<?php

namespace App\Models\MySql;

class MetricsReport extends Base
{
    protected $table = 'metrics_report';
    const TABLE = 'metrics_report';

    protected $guarded = [
        'id',
    ];

    const IS_DEFAULT = 1;
    const IS_DEFAULT_NOT = 0;

    const KIND_FULL_BLACK      = 1; // 综合报表 黑盒
    const KIND_FULL            = 2; // 综合报表 白盒
    const KIND_BOTH            = 3; // 综合报表 黑白盒 （废弃）
    const KIND_LTV             = 4;
    const KIND_RETENTION       = 5;
    const KIND_MY_OFFER        = 6;
    const KIND_FULL_ADMIN      = 7;
    const KIND_WATERFALL       = 8;
    const KIND_APP             = 9;
    const KIND_APP_BLACK       = 10;
    const KIND_PLACEMENT       = 11;
    const KIND_PLACEMENT_BLACK = 12;

    private static $kindMap;

    public static function getKindMap(): array
    {
        return [
            self::KIND_FULL_BLACK      => '综合报表黑盒',
            self::KIND_FULL            => '综合报表白盒',
            self::KIND_BOTH            => '综合报表黑白盒', // 已废弃
            self::KIND_LTV             => 'LTV',
            self::KIND_RETENTION       => '留存',
            self::KIND_MY_OFFER        => 'My Offer',
            self::KIND_FULL_ADMIN      => '综合报表(运营后台)',
            self::KIND_WATERFALL       => '聚合管理',
            self::KIND_APP             => 'App 白盒',
            self::KIND_APP_BLACK       => 'App 黑盒',
            self::KIND_PLACEMENT       => 'Placement 白盒',
            self::KIND_PLACEMENT_BLACK => 'Placement 黑盒',
        ];
    }

    public static function getKindName($kind) {
        if (self::$kindMap == null) {
            self::$kindMap = self::getKindMap();
        }
        return self::$kindMap[$kind] ?? '';
    }

    public static function getFullReportFields(){
        return self::query()->where('kind', 7)
            ->orderBy('show_priority', 'desc')
            ->get()
            ->toArray();
    }
}
