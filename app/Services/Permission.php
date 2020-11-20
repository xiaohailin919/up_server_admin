<?php

namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Permission
{
    /**
     * 判断是否有页面访问权限，否则抛出“无访问权限”的异常
     *
     * @param $permissionName
     */
    public static function checkAccessPermission($permissionName = '') {

        if (empty($permissionName)) {
            $permissionName = self::getRequiredPermission();
        }

        if ($permissionName != '') {
            $user = auth('api')->user();
            if ($user === null) {
                throw new HttpException(500, __('code.' . 9994), null, [], 9994);
            }
            assert($user instanceof User);
            if (!$user->hasPermissionTo($permissionName)) {
                throw new HttpException(403, __('code.' . 9003), null, [], 9003);
            }
        }
    }

    /**
     * 根据访问的资源，从配置中解析出所需要的权限
     *
     * @return string
     */
    protected static function getRequiredPermission(): string
    {
        $action = request()->route()->getAction();
        $actionPath = $action['uses'];
        $pos = strpos($actionPath, '@');
        $controller = substr($actionPath, 0, $pos);
        $actionName = substr($actionPath, $pos + 1);

        return config('permission.map.' . $controller . '.' . $actionName, '');
    }

    /**
     * 获取当前登陆用户的权限
     * @return array
     */
    public static function getPermissions()
    {
        //获取当前用户的所有权限
        $permissionList = $permissions = [];
        $user = Auth::user();
        if ($user) {
            $permissions = $user->getAllPermissions();
        }

        if (!$permissions) {
            return [];
        }

        foreach($permissions as $permission){
            $permissionList[] = $permission->name;
        }

        //获取所有的权限
        $list = $all = self::getAllPermissions();
        
        //格式化权限列表
        $list = self::buildPermissionsArray($list, $permissionList);
        
        return $list;
    }

    public static function buildPermissionsArray($permissions, $already = [])
    {
        if (!$already) {
            return [];
        }
        $array = [];
        foreach($permissions as $key => &$val){
            if((isset($val['show']) && !$val['show']) || (isset($val['permission_name']) && !in_array($val['permission_name'], $already))){
                continue;
            }
            if(isset($val['list']) && !empty($val['list'])){
                $val['list'] = self::buildPermissionsArray($val['list'], $already);
            }
            if(empty($val)
                || (isset($val['list']) && !$val['list'])){
                continue;
            }
            $array[$key] = $val;
        }
        return $array;
    }
    
    /**
     * 获取全部权限
     *
     * @deprecated
     * @return array
     */
    public static function getAllPermissions()
    {
        return [
            'publishers' => [
                'icon' => '<i class="fi-content-left"></i>',
                'name' => 'Publishers',
                'list' => [
                    [
                        'name' => 'Manage Publishers',
                        'route' => 'publisher',
                        'font_end' => '/publishers/manage-publisher',
                        'permission_name' => 'publisher_list',
                        'show' => true,
                    ],

                    [
                        'name' => 'Publisher Edit',
                        'route' => '#',
                        'permission_name' => 'publisher_edit',
                        'show' => false,
                    ],

                    [
                        'name' => 'Publisher Activate',
                        'route' => 'publisher/activate',
                        'permission_name' => 'publisher_activate',
                        'show' => false,
                    ],

                    [
                        'name' => 'Publisher Group',
                        'route' => '#',
                        'permission_name' => 'publisher-group@index',
                        'show' => false,
                    ],

                    [
                        'name' => 'Publisher Group Store',
                        'route' => '#',
                        'permission_name' => 'publisher-group@store',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Apps',
                        'route' => 'app',
                        'font_end' => '/publishers/manage-app',
                        'permission_name' => 'app_list',
                        'show' => true,
                    ],

                    [
                        'name' => 'Manage App Label',
                        'route' => 'app-term',
                        'font_end' => '/publishers/manage-app-label',
                        'permission_name' => 'app-term@index',
                        'show' => true,
                    ],

                    [
                        'name' => 'Manage App Label Store',
                        'route' => '#',
                        'permission_name' => 'app-term@store',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage App Label Update',
                        'route' => '#',
                        'permission_name' => 'app-term@update',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage App Label Destroy',
                        'route' => '#',
                        'permission_name' => 'app-term@destroy',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Placements',
                        'route' => 'placement',
                        'font_end' => '/publishers/manage-placement',
                        'permission_name' => 'placement_list',
                        'show' => true,
                    ],

                    [
                        'name' => 'Edit Placements',
                        'route' => '#',
                        'permission_name' => 'placement_edit',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Revenue (Main Account)',
                        'route' => 'revenue-deduction',
                        'show' => true,
                        'permission_name' => 'revenue_deduction',
                    ],

                    [
                        'name' => 'Manage Revenue (Sub Account)',
                        'route' => 'revenue-deduction-sub-account?dimension=app-placement',
                        'show' => true,
                        'permission_name' => 'revenue_deduction',
                    ],

                    [
                        'name' => 'Manage Manager Revenue Add',
                        'route' => 'revenue-deduction/add',
                        'permission_name' => 'revenue_deduction_add',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Manager Revenue Edit',
                        'route' => 'revenue-deduction',
                        'permission_name' => 'revenue_deduction_edit',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Impression',
                        'route' => 'manage-impression',
                        'permission_name' => 'impression_deduction',
                        'show' => true,
                    ],

                    [
                        'name' => 'Manage Fill Rate',
                        'route' => 'manage-fill-rate',
                        'permission_name' => 'fill_rate_deduction',
                        'show' => true,
                    ],

                    [
                        'name' => 'Manage Firm',
                        'route' => 'network-firm',
                        'font_end' => 'publishers/manage-firm',
                        'permission_name' => 'network-firm@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Manage Firm Store',
                        'route' => '#',
                        'permission_name' => 'network-firm@store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Manage Firm Update',
                        'route' => '#',
                        'permission_name' => 'network-firm@update',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Firm Adapter',
                        'route' => 'firm-adapter',
                        'font_end' => 'publishers/manage-firm-adapter',
                        'permission_name' => 'firm-adapter@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Manage Firm Adapter Store',
                        'route' => '#',
                        'permission_name' => 'firm-adapter@store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Manage Firm Adapter Edit',
                        'route' => '#',
                        'permission_name' => 'firm-adapter@update',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Report Metrics',
                        'route' => 'metrics-report',
                        'font_end' => 'publishers/manage-report-metrics',
                        'permission_name' => 'metrics_report@index',
                        'show' => true,
                    ],

                    [
                        'name' => 'Manage Report Metrics Store',
                        'route' => '#',
                        'permission_name' => 'metrics_report@store',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Report Metrics Update',
                        'route' => '#',
                        'permission_name' => 'metrics_report@update',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Report Metrics Destroy',
                        'route' => '#',
                        'permission_name' => 'metrics_report@destroy',
                        'show' => false,
                    ],

                    [
                        'name' => 'Manage Report API',
                        'route' => 'report-unit-log',
                        'show' => true,
                        'permission_name' => 'report_unit_log',
                    ],

                    [
                        'name' => 'Manage Report API',
                        'route' => 'report-unit-log/add',
                        'permission_name' => 'report_unit_log_add',
                        'show' => false,
                    ],

                    [
                        'name' => 'Report API Rules',
                        'route' => 'network-crawl',
                        'permission_name' => 'network-crawl@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Report API Rules Store',
                        'route' => '#',
                        'permission_name' => 'network-crawl@store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Report API Rules Update',
                        'route' => '#',
                        'permission_name' => 'network-crawl@update',
                        'show' => false,
                    ],
                    [
                        'name' => 'Report API Rules Delete',
                        'route' => '#',
                        'permission_name' => 'network-crawl@delete',
                        'show' => false,
                    ],

                    [
                        'name' => 'App Edit',
                        'route' => '#',
                        'permission_name' => 'app_edit',
                        'show' => false,
                    ],

                    [
                        'name' => 'Upload Network Report',
                        'route' => 'report-import',
                        'permission_name' => 'report_import',
                        'show' => true,
                    ],

                    [
                        'name' => 'Upload Network Report Edit',
                        'route' => '#',
                        'permission_name' => 'report_import_edit',
                        'show' => false,
                    ],

                    [
                        'name' => 'Change Logs',
                        'route' => 'unit-change-log',
//                        'font_end' => 'publishers/change-log',
                        'show' => true,
                        'permission_name' => 'unit_change_log',
                    ],

                    [
                        'name' => 'Change Logs Detail',
                        'route' => '#',
                        'permission_name' => 'unit_change_log',
                        'show' => false,
                    ],
                ],
            ],

            'adx' => [
                'icon' => '<i class="fa fa-shopping-bag"></i>',
                'name' => 'Adx',
                'list' =>[
                    [
                        'name' => 'ADX上游管理',
                        'font_end' => '/adx/demand',
                        'permission_name' => 'adx-demand@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'ADX广告管理',
                        'font_end' => '/adx/index',
                        'permission_name' => 'adx-bw-list@index',
                        'show' => true,
                    ],
                ]
            ],

            'strategy' => [
                'icon' => '<i class="fi-box"></i>',
                'name' => 'Strategy',
                'list' => [
                    [
                        'name' => 'APP',
                        'route' => 'strategy-app',
                        'font_end' => '/strategy/app',
                        'permission_name' => 'strategy_app',
                        'show' => true,
                    ],
                    [
                        'name' => 'APP strategy edit',
                        'route' => '#',
                        'permission_name' => 'strategy_app_edit',
                        'show' => false,
                    ],
                    [
                        'name' => 'Placement',
                        'route' => 'strategy-placement',
                        'permission_name' => 'strategy_placement',
                        'show' => true,
                    ],
                    [
                        'name' => 'Placement strategy edit',
                        'route' => '#',
                        'permission_name' => 'strategy_placement_edit',
                        'show' => false,
                    ],
                    //upload-rules
                    [
                        'name' => 'Upload Rules',
                        'route' => 'upload-rules',
                        'font_end' => '/strategy/upload-rules',
                        'permission_name' => 'upload_rules',
                        'show' => true,
                    ],
                    [
                        'name' => 'Upload Rules Store',
                        'route' => '#',
                        'permission_name' => 'upload_rules_store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Upload Rules Update',
                        'route' => '#',
                        'permission_name' => 'upload_rules_update',
                        'show' => false,
                    ],

                    // tc upload rules
                    [
                        'name' => 'TC Upload Rules',
                        'route' => 'tc-upload-rule',
                        'permission_name' => 'tc_upload_rules',
                        'show' => true,
                    ],
                    [
                        'name' => 'TC Upload Rules Store',
                        'route' => '#',
                        'permission_name' => 'tc_upload_rules_store',
                        'show' => false,
                    ],
                    [
                        'name' => 'TC Upload Rules Update',
                        'route' => '#',
                        'permission_name' => 'tc_upload_rules_update',
                        'show' => false,
                    ],

                    // tc mapping rule
                    [
                        'name' => 'TC Mapping Rules',
                        'route' => 'tc-mapping-rule',
                        'permission_name' => 'tc_mapping_rules',
                        'show' => true,
                    ],
                    [
                        'name' => 'TC Mapping Rules Store',
                        'route' => '#',
                        'permission_name' => 'tc_mapping_rules_store',
                        'show' => false,
                    ],
                    [
                        'name' => 'TC Mapping Rules Update',
                        'route' => '#',
                        'permission_name' => 'tc_mapping_rules_update',
                        'show' => false,
                    ],
                    [
                        'name' => 'TC Rate Rules',
                        'route' => 'tc-strategy',
                        'permission_name' => 'tc_strategy',
                        'show' => true,
                    ],
                    [
                        'name' => 'My Offer Strategy',
                        'route' => 'strategy-placement-my-offer',
                        'permission_name' => 'strategy_placement_my_offer',
                        'show' => true,
                    ],

                    // strategy-app-firm switch
                    [
                        'name' => 'Strategy APP Firm Switch',
                        'route' => 'strategy-app-firm-switch',
                        'permission_name' => 'strategy_app_firm_switch',
                        'show' => false,
                    ],
                    [
                        'name' => 'Strategy APP Firm Switch edit',
                        'route' => '#',
                        'permission_name' => 'strategy_app_firm_switch_edit',
                        'show' => false,
                    ],
                    [
                        'name' => 'Strategy APP Firm Switch store',
                        'route' => '#',
                        'permission_name' => 'strategy_app_firm_switch_store',
                        'show' => false,
                    ],
                    // Ads Visibility SDK
                    [
                        'name' => 'Ads Visibility SDK',
                        'route' => 'strategy-plugin',
                        'permission_name' => 'strategy_plugin',
                        'show' => true,
                    ],
                    [
                        'name' => 'Ads Visibility SDK create',
                        'route' => '#',
                        'permission_name' => 'strategy_plugin_store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Ads Visibility SDK edit',
                        'route' => '#',
                        'permission_name' => 'strategy_plugin_edit',
                        'show' => false,
                    ],
                    [
                        'name' => 'Ads Visibility SDK update',
                        'route' => '#',
                        'permission_name' => 'strategy_plugin_update',
                        'show' => false,
                    ],
                    [
                        'name' => 'Ads Visibility SDK Whit List',
                        'route' => '#',
                        'permission_name' => 'strategy_plugin_white_list',
                        'show' => false,
                    ],
                    [
                        'name' => 'SDK Manage',
                        'font_end' => '/strategy/manage-sdk-version',
                        'permission_name' => 'sdk-manage@index',
                        'show' => true
                    ],
                    [
                        'name' => 'SDK Manage Update',
                        'font_end' => '',
                        'permission_name' => 'sdk-manage@update',
                        'show' => false
                    ],
                    [
                        'name' => 'SDK Manage Store',
                        'font_end' => '',
                        'permission_name' => 'sdk-manage@store',
                        'show' => false
                    ],
                    [
                        'name' => 'SDK Distribution',
                        'route' => 'strategy-sdk-distribution',
                        'font_end' => '/strategy/sdk-distribution',
                        'permission_name' => 'strategy-sdk-distribution@index',
                        'show' => true
                    ],
                    [
                        'name' => 'SDK Distribution Store',
                        'route' => '#',
                        'permission_name' => 'strategy-sdk-distribution@store',
                        'show' => false
                    ],
                    [
                        'name' => 'SDK Distribution Update',
                        'route' => '#',
                        'permission_name' => 'strategy-sdk-distribution@update',
                        'show' => false
                    ],
                    [
                        'name' => 'SDK Distribution Destroy',
                        'route' => '#',
                        'permission_name' => 'strategy-sdk-distribution@destroy',
                        'show' => false
                    ],
                    [
                        'name' => 'Placement Firm',
                        'route' => 'strategy-firm',
                        'permission_name' => 'strategy-firm@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Placement Firm Store',
                        'route' => '#',
                        'permission_name' => 'strategy-firm@store',
                        'show' => false,
                    ],
                    [
                        'name' => 'Placement Firm Edit',
                        'route' => '#',
                        'permission_name' => 'strategy-firm@edit',
                        'show' => false,
                    ],
                    [
                        'name' => 'Placement Firm Update',
                        'route' => '#',
                        'permission_name' => 'strategy-firm@update',
                        'show' => false,
                    ],

                    [
                        'name' => 'ADX策略管理',
                        'font_end' => '/strategy/adx-offer',
                        'permission_name' => 'adx-strategy@index',
                        'show' => true,
                    ],
                ],
            ],

            'reports' => [
                'icon' => '<i class="fi-bar-graph-2"></i>',
                'name' => 'Reports',
                'list' => [
                    [
                        'name' => 'Full Report',
                        'route' => 'report-full',
                        'permission_name' => 'full_report_list',
                        'show' => true,
                    ],
                    [
                        'name' => 'Chart Report',
                        'route' => 'chart-report-v2',
                        'permission_name' => 'chart_report_list',
                        'show' => true,
                    ],
                    [
                        'name' => 'Tc Report',
                        'route' => 'report-tc',
                        'permission_name' => 'report-tc@index',
                        'show' => true,
                    ]
                ],
            ],

            'contents' => [
                'icon' => '<i class="fi-content-left"></i>',
                'name' => 'Contents',
                'list' => [
                    [
                        'name' => 'News & Events',
                        'route' => 'posts',
                        'font_end' => 'contents/news-events',
                        'permission_name' => 'posts@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Categories & Tags',
                        'font_end' => 'contents/categories-tags',
                        'permission_name' => 'posts-term@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Contact',
                        'font_end' => 'contents/contacts',
                        'permission_name' => 'contact@index',
                        'show' => true,
                    ],
                    [
                        'name' => 'Subscribers',
                        'route' => 'subscribers',
                        'font_end' => 'contents/subscribers',
                        'permission_name' => 'subscribers@index',
                        'show' => true,
                    ],
                ],
            ],

            'admin' => [
                'icon' => '<i class="fi-head"></i>',
                'name' => 'Admin',
                'list' => [
                    [
                        'name' => 'Create User',
                        'route' => '#',
                        'font_end' => '/admin/manage-user/add?type=add',
                        'permission_name' => 'Administer roles & permissions',
                        'show' => true,
                    ],

                    [
                        'name' => 'Users',
                        'route' => 'users',
                        'font_end' => '/admin/manage-user',
                        'permission_name' => 'Administer roles & permissions',
                        'show' => true,
                    ],

                    [
                        'name' => 'Permissions',
                        'route' => 'permissions',
                        'font_end' => '/admin/permissions',
                        'permission_name' => 'Administer roles & permissions',
                        'show' => true,
                    ],

                    [
                        'name' => 'Roles',
                        'route' => 'roles',
                        'font_end' => '/admin/roles',
                        'permission_name' => 'Administer roles & permissions',
                        'show' => true,
                    ]
                ],
            ],
        ];
    }
}
