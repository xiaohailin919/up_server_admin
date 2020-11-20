<?php

return [

    'models' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    /*
     * By default all permissions will be cached for 24 hours unless a permission or
     * role is updated. Then the cache will be flushed immediately.
     */

    'cache_expiration_time' => 60 * 24,


    /*
     * 各控制器对应方法的权限配置，如果没有配置，这该方法不需要进行权限校验
     */
    'map' => [

        /* APP 管理页 */
        \App\Http\Controllers\AppController::class => [
            'index' => 'app_list',
        ],

        /* 主账号补扣量 */
        \App\Http\Controllers\RevenueDeductionController::class => [
            'index'  => 'revenue_deduction',
            'store'  => 'revenue_deduction_add',
            'show'   => 'revenue_deduction_edit',
            'update' => 'revenue_deduction_edit',
            'rerun'  => 'revenue_deduction_edit',
        ],

        /* 子账号补扣量 */
        \App\Http\Controllers\RevenueDeductionSubAccountController::class => [
            'index'  => 'revenue_deduction',
            'store'  => 'revenue_deduction_add',
            'show'   => 'revenue_deduction_edit',
            'update' => 'revenue_deduction_edit'
        ],

        /* 补扣量 */
        \App\Http\Controllers\DeductionRuleController::class => [
            'index'  => 'deduction-rule@index',
            'store'  => 'deduction-rule@store',
            'show'   => 'deduction-rule@show',
            'update' => 'deduction-rule@update',
        ],

        /* API 数据拉取配置 */
        \App\Http\Controllers\NetworkCrawlController::class => [
            'index'   => 'network-crawl@index',
            'show'    => 'network-crawl@index',
            'update'  => 'network-crawl@update',
            'store'   => 'network-crawl@store',
            'destroy' => 'network-crawl@delete',
        ],

        /* 手动上传报表 */
        \App\Http\Controllers\ReportImportController::class => [
            'index' => 'report_import',
        ],

        /* Unit change log 操作日志管理 */
        \App\Http\Controllers\UnitChangeLogController::class => [
            'index' => 'unit_change_log',
            'show'  => 'unit_change_log',
        ],

        \App\Http\Controllers\StrategyAppController::class => [
            'index' => 'strategy_app',
            'show' => 'strategy_app_edit',
            'update' => 'strategy_app_edit',
        ],

        /* Strategy Placement */
        \App\Http\Controllers\StrategyPlacementController::class => [
            'index'  => 'strategy_placement',
            'show'   => 'strategy_placement',
            'update' => 'strategy_placement_edit',
        ],

        /* Strategy Placement Firm */
        \App\Http\Controllers\StrategyPlacementFirmController::class => [
            'index'  => 'strategy_placement',
            'create' => 'strategy_placement',
            'show'   => 'strategy_placement',
            'store'  => 'strategy_placement_edit',
            'update' => 'strategy_placement_edit',
        ],
    
        /* SDK Channel SDK定制渠道号 */
        \App\Http\Controllers\SdkChannelController::class => [
            'index'  => 'sdk-channel@index',
            'show'   => 'sdk-channel@show',
            'store'  => 'sdk-channel@store',
            'update' => 'sdk-channel@update',
        ],
        /* SDK Strategy  SDK定制渠道策略 */
        \App\Http\Controllers\SdkInhouseStrategyController::class => [
            'index'  => 'sdk-inhouse-strategy@index',
            'show'   => 'sdk-inhouse-strategy@show',
            'store'  => 'sdk-inhouse-strategy@store',
            'update' => 'sdk-inhouse-strategy@update',
        ],
        /* Tc Strategy */
        \App\Http\Controllers\TcStrategyController::class => [
            'index'  => 'tc_strategy',
            'show'   => 'tc_strategy',
            'store'  => 'tc_strategy',
            'update' => 'tc_strategy',
        ],

        /* Strategy MyOffer */
        \App\Http\Controllers\StrategyPlacementMyOfferController::class => [
            'index' => 'strategy_placement_my_offer',
        ],

        \App\Http\Controllers\StrategyPluginController::class => [
            'index'           => 'strategy_plugin',
            'show'            => 'strategy_plugin',
            'create'          => 'strategy_plugin_store',
            'store'           => 'strategy_plugin_store',
            'update'          => 'strategy_plugin_update',
            'whiteList'       => 'strategy_plugin',
            'updateWhiteList' => 'strategy_plugin_white_list',
        ],

        /* Strategy Firm */
        \App\Http\Controllers\StrategyFirmController::class => [
            'index'  => 'strategy-firm@index',
            'store'  => 'strategy-firm@store',
            'show'   => 'strategy-firm@update',
            'update' => 'strategy-firm@update',
        ],
        
        /* Report Chart */
        \App\Http\Controllers\ReportChartApiController::class => [
            'index'  => 'chart_report_list',
            'export' => 'chart_report_list',
        ],
        
        /* Report TC */
        \App\Http\Controllers\ReportTcApiController::class => [
            'index'  => 'report-tc@index',
            'export' => 'report-tc@index',
        ],
        
        /* Users 用户管理 */
        \App\Http\Controllers\UserController::class => [
            'index'   => 'Administer roles & permissions',
            'store'   => 'Administer roles & permissions',
            'show'    => 'Administer roles & permissions',
            'update'  => 'Administer roles & permissions',
            'destroy' => 'Administer roles & permissions',
        ],
    ]
];
