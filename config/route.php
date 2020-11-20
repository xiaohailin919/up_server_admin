<?php

return [
    /* 流量管理 */
    [
        'icon' => '<i class="fi-content-left"></i>',
        'name' => 'Publishers',
        'list' => [
            [
                'name' => 'Manage Publishers',
                'route' => 'redirect?dest=/publishers/manage-publisher',
                'permission' => 'publisher_list',
            ],
            [
                'name' => 'Manage Apps',
                'route' => 'redirect?dest=/publishers/manage-app',
                'permission' => 'app_list',
            ],
            [
                'name' => 'Manage App Label',
                'route' => 'redirect?dest=/publishers/manage-app-label',
                'permission' => 'app-term@index',
            ],
            [
                'name' => 'Manage Placements',
                'route' => 'redirect?dest=/publishers/manage-placement',
                'permission' => 'placement_list',
            ],
            [
                'name' => 'Manage Revenue (Main Account)',
                'route' => 'redirect?dest=/publishers/revenue-deduction-main-account',
                'permission' => 'revenue_deduction',
            ],
            [
                'name' => 'Manage Revenue (Sub Account)',
                'route' => 'redirect?dest=/publishers/revenue-deduction-sub-account',
                'permission' => 'revenue_deduction',
            ],
            [
                'name' => 'Manage Impression',
                'route' => 'redirect?dest=/publishers/manage-impression',
                'permission' => 'deduction-rule@index',
            ],
            [
                'name' => 'Manage Fill Rate',
                'route' => 'redirect?dest=/publishers/manage-fill-rate',
                'permission' => 'deduction-rule@index',
            ],
            [
                'name' => 'Manage Firm',
                'route' => 'redirect?dest=/publishers/manage-firm',
                'permission' => 'network-firm@index',
            ],
            [
                'name' => 'Manage Firm Adapter',
                'route' => 'redirect?dest=/publishers/manage-firm-adapter',
                'permission' => 'firm-adapter@index',
            ],
            [
                'name' => 'Manage Report Metrics',
                'route' => 'redirect?dest=/publishers/manage-report-metrics',
                'permission' => 'metrics_report@index',
            ],
            [
                'name' => 'Manage Report API',
                'route' => 'redirect?dest=publishers/manage-report-api',
                'permission' => 'report_unit_log',
            ],
            [
                'name' => 'Report API Rules',
                'route' => 'redirect?dest=/publishers/report-api-rules',
                'permission' => 'network-crawl@index',
            ],
            [
                'name' => 'Upload Network Report',
                'route' => 'redirect?dest=/publishers/upload-network-report',
                'permission' => 'report_import',
            ],
            [
                'name' => 'Change Logs',
                'route' => 'redirect?dest=/publishers/change-log',
                'permission' => 'unit_change_log',
            ],
        ],
    ],

    /* ADX */
    [
        'icon' => '<i class="fa fa-shopping-bag"></i>',
        'name' => 'Adx',
        'list' =>[
            [
                'name' => 'ADX上游管理',
                'route' => 'redirect?dest=/adx/demand',
                'permission' => 'adx-demand@index',
            ],
            [
                'name' => 'ADX广告管理',
                'route' => 'redirect?dest=/adx/index',
                'permission' => 'adx-bw-list@index',
            ],
        ]
    ],

    /* 策略管理 */
    [
        'icon' => '<i class="fi-box"></i>',
        'name' => 'Strategy',
        'list' => [
            [
                'name' => 'APP',
                'route' => 'redirect?dest=/strategy/app',
                'permission' => 'strategy_app',
            ],
            [
                'name' => 'Placement',
                'route' => 'redirect?dest=/strategy/placement-strategy',
                'permission' => 'strategy_placement',
            ],
            [
                'name' => 'Upload Rules',
                'route' => 'redirect?dest=/strategy/upload-rules',
                'permission' => 'upload_rules',
            ],
            [
                'name' => 'TC Upload Rules',
                'route' => 'tc-upload-rule',
                'permission' => 'tc_upload_rules',
            ],
            [
                'name' => 'TC Mapping Rules',
                'route' => 'tc-mapping-rule',
                'permission' => 'tc_mapping_rules',
            ],
            [
                'name' => 'TC Rate Rules',
                'route' => 'redirect?dest=/strategy/tc-strategy',
                'permission' => 'tc_strategy',
            ],
            [
                'name' => 'My Offer Strategy',
                'route' => 'redirect?dest=/strategy/myoffer-strategy',
                'permission' => 'strategy_placement_my_offer',
            ],
            [
                'name' => 'Ads Visibility SDK',
                'route' => 'redirect?dest=/strategy/plugin-strategy',
                'permission' => 'strategy_plugin',
            ],
            [
                'name' => 'SDK Manage',
                'route' => 'redirect?dest=/strategy/manage-sdk-version',
                'permission' => 'sdk-manage@index',
            ],
            [
                'name' => 'SDK Distribution',
                'route' => 'redirect?dest=/strategy/sdk-distribution',
                'permission' => 'strategy-sdk-distribution@index',
            ],
            [
                'name' => 'Placement Firm',
                'route' => 'redirect?dest=/strategy/firm-strategy',
                'permission' => 'strategy-firm@index',
            ],
            [
                'name' => 'Adx & OnlineAPI Offer Strategy',
                'route' => 'redirect?dest=/strategy/adx-offer',
                'permission' => 'adx-strategy@index',
            ],
        ],
    ],

    /* 报表查询 */
    [
        'icon' => '<i class="fi-bar-graph-2"></i>',
        'name' => 'Reports',
        'list' => [
            [
                'name' => 'Full Report',
                'route' => 'report-full',
                'permission' => 'full_report_list',
            ],
            [
                'name' => 'Chart Report',
                'route' => 'redirect?dest=/reports/chart-report-v2',
                'permission' => 'chart_report_list',
            ],
            [
                'name' => 'Tc Report',
                'route' => 'report-tc',
                'permission' => 'report-tc@index',
            ]
        ],
    ],

    /* 内容 */
    [
        'icon' => '<i class="fi-content-left"></i>',
        'name' => 'Contents',
        'list' => [
            [
                'name' => 'News & Events',
                'route' => 'redirect?dest=/contents/news-events',
                'permission' => 'posts@index',
            ],
            [
                'name' => 'Categories & Tags',
                'route' => 'redirect?dest=/contents/categories-tags',
                'permission' => 'posts-term@index',
            ],
            [
                'name' => 'Contact',
                'route' => 'redirect?dest=/contents/contacts',
                'permission' => 'contact@index',
            ],
            [
                'name' => 'Subscribers',
                'route' => 'redirect?dest=/contents/subscribers',
                'permission' => 'subscribers@index',
            ],
        ],
    ],

    /* 管理员 */
    [
        'icon' => '<i class="fi-head"></i>',
        'name' => 'Admin',
        'list' => [
            [
                'name' => 'Create User',
                'route' => 'redirect?dest=/admin/manage-user/add?type=add',
                'permission' => 'Administer roles & permissions',
            ],
            [
                'name' => 'Users',
                'route' => 'redirect?dest=/admin/manage-user',
                'permission' => 'Administer roles & permissions',
            ],
            [
                'name' => 'Permissions',
                'route' => 'redirect?dest=/admin/permissions',
                'permission' => 'Administer roles & permissions',
            ],
            [
                'name' => 'Roles',
                'route' => 'redirect?dest=/admin/roles',
                'permission' => 'Administer roles & permissions',
            ]
        ],
    ],
];