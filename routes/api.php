<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* auth:api，表示使用 auth 中间件，且传递参数为 “api”，语法：middleware:param1,param2,param3 */
Route::middleware('auth:api')->get('/user', static function (Request $request) {
    return $request->user();
});

// 认证路由组
Route::prefix('auth')->group(static function () {
    Route::post('ticket', 'AuthController@ticketLogin');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('password', 'AuthController@updatePassword');
});

// 用户相关
Route::get('email-signature', 'UserController@getEmailSignature');
Route::post('email-signature', 'UserController@setEmailSignature');
Route::get('user/meta', 'UserController@meta');
Route::resource('user', 'UserController');

// 角色相关
Route::get('role/meta', 'MetaInfoController@role');
Route::resource('role', 'RoleController');

// 权限相关
Route::get('permission/meta/recursive', 'PermissionController@metaRecursive');
Route::get('permission/meta', 'PermissionController@meta');
Route::resource('permission', 'PermissionController');

// 数据权限相关
Route::get('data-role/meta', 'MetaInfoController@dataRoleNesting');
Route::get('data-role/meta-secondary', 'MetaInfoController@dataRoleSecondary');
Route::resource('data-role', 'DataRoleController');

// 报表相关
Route::get('report-full', 'ReportFullApiController@index');
Route::post('report-chart', 'ReportChartApiController@index');
Route::post('report-chart/export', 'ReportChartApiController@index'); //导出暂时方案
Route::post('report-tc', 'ReportTcApiController@index');
Route::any('report-tc/export', 'ReportTcApiController@index');  //导出暂时方案
Route::get('report-tc/dimensions', 'ReportTcApiController@dimensions');

// Publisher 相关
Route::get('publisher/meta/custom-adapter', 'PublisherApiController@metaCustomAdapter');
Route::get('publisher/meta/custom-network', 'PublisherApiController@metaCustomNetwork');
Route::get('publisher/network-firm', 'PublisherApiController@getNetworkFirm');
Route::post('publisher/network-firm', 'PublisherApiController@updateNetworkFirm');
Route::post('publisher/multi', 'PublisherApiController@multipleUpdate');
Route::get('publisher/login', 'PublisherApiController@login');
Route::get('publisher/export', 'PublisherApiController@export');
Route::resource('publisher', 'PublisherApiController');

// Publisher Group 相关
Route::get('publisher-group/meta', 'MetaInfoController@publisherGroup');
Route::resource('publisher-group', 'PublisherGroupController');

// App 相关
Route::get('app-term/export', 'AppTermController@export');
Route::resource('app-term', 'AppTermController');

Route::post('app-term-relationship', 'AppTermRelationshipController@store');
Route::put('app-term-relationship/label/multi', 'AppTermRelationshipController@multiReplaceLabel');
Route::post('app-term-relationship/type/multi', 'AppTermRelationshipController@multiStoreType');
Route::post('app-term-relationship/label/multi', 'AppTermRelationshipController@multiStoreLabel');
Route::delete('app-term-relationship/type/multi', 'AppTermRelationshipController@multiDestroyType');
Route::delete('app-term-relationship/label/multi', 'AppTermRelationshipController@multiDestroyLabel');

Route::get('app/term-list', 'AppController@termList');
Route::get('app/time-map', 'AppController@getTimeMap');
Route::get('app/export', 'AppController@export');
Route::resource('app', 'AppController');

// Placement
Route::get('placement/export', 'PlacementController@export');
Route::resource('placement', 'PlacementController');

// 主账号补扣量
Route::post('revenue-deduction/main/rerun/{id}', 'RevenueDeductionController@rerun');
Route::resource('revenue-deduction/main', 'RevenueDeductionController');

// 子账号补扣量
Route::post('revenue-deduction/sub/rerun', 'RevenueDeductionSubAccountController@rerun');
Route::resource('revenue-deduction/sub', 'RevenueDeductionSubAccountController');

// 补扣量规则
Route::resource('deduction-rule', 'DeductionRuleController');

// Network Firm
Route::get('network-firm/meta/non-custom', 'NetworkFirmController@metaNonCustom');
Route::get('network-firm/meta/custom', 'NetworkFirmController@metaCustom');
Route::get('network-firm/meta/all', 'NetworkFirmController@metaAll');
Route::get('network-firm/meta', 'NetworkFirmController@meta');
Route::get('network-firm/id', 'NetworkFirmController@nextNetworkFirmId');
Route::resource('network-firm', 'NetworkFirmController');

// Firm Adapter
Route::get('firm-adapter/get', 'FirmAdapterController@firmPublisherAdapters');
Route::resource('firm-adapter', 'FirmAdapterController');

// 报表指标管理
Route::resource('metrics-report', 'MetricsReportController');

// Unit change Log
Route::resource('unit-change-log', 'UnitChangeLogController');

// 报表 API 管理 Report Uint Log
Route::resource('report-unit-log', 'ReportUnitLogController');

// API 数据拉取配置 Report Api Rules
Route::get('network-crawl/meta/pull-time', 'NetworkCrawlController@metaPullTime');
Route::get('network-crawl/meta/nw-firm', 'NetworkCrawlController@metaNwFirm');
Route::delete('network-crawl', 'NetworkCrawlController@destroy');
Route::resource('network-crawl', 'NetworkCrawlController');

// 手动上传报表
Route::get('report-import/meta/network-firm', 'ReportImportController@metaNwFirm');
Route::resource('report-import', 'ReportImportController');

// Strategy App
Route::resource('strategy-app', 'StrategyAppController');

// Strategy Placement
Route::resource('strategy-placement', 'StrategyPlacementController');

// Strategy Placement Firm
Route::resource('strategy-placement-firm', 'StrategyPlacementFirmController');

// Tc Strategy
Route::resource('strategy-tc', 'TcStrategyController');

// Strategy MyOffer 交叉推广
Route::resource('strategy-placement-my-offer', 'StrategyPlacementMyOfferController');

// Strategy Plugin 独立插件
Route::put('strategy-plugin/white-list', 'StrategyPluginController@updateWhiteList');
Route::get('strategy-plugin/white-list', 'StrategyPluginController@whiteList');
Route::resource('strategy-plugin', 'StrategyPluginController');

// Strategy Firm
Route::resource('strategy-firm', 'StrategyFirmController');


// SDK定制版本渠道号
Route::get('sdk-channel/meta/all','SdkChannelController@metaAll');
Route::resource('sdk-channel','SdkChannelController');
// SDK定制版本渠道策略
Route::resource('sdk-inhouse-strategy','SdkInhouseStrategyController');


// ADX Demand
Route::get('adx-demand/meta', 'AdxDemandController@meta');
Route::put('adx-demand/{id}/status', 'AdxDemandController@status');
Route::resource('adx-demand', 'AdxDemandController');
// ADX B/W List
Route::get('adx-bw-list/category', 'AdxBwListController@category');
Route::resource('adx-bw-list', 'AdxBwListController');
// ADX Strategy
Route::resource('adx-strategy', 'AdxStrategyController');

// Post 相关：News、Event、Report、Email
Route::get('test/{id}', 'PostsController@emailView');
Route::get('post/term', 'PostsTermController@meta'); // 临时留着，前端改好后删除
Route::post('post/email/{id}', 'PostsController@sendEmail');
Route::post('post/test-email/{id}', 'PostsController@testEdmEmail');
Route::resource('post', 'PostsController');
// Post Term 相关
Route::get('post-term/meta', 'PostsTermController@meta');
Route::resource('post-term', 'PostsTermController');
// Contact 相关
Route::get('contact/export', 'ContactController@export');
Route::get('contact/meta/contactor', 'ContactController@metaContactor');
Route::resource('contact', 'ContactController');
// Subscriber 相关
Route::resource('subscriber', 'SubscribersController');

// TK 埋点上报规则管理
Route::resource('upload-rule', 'UploadRulesController');

// SDK 分发策略
Route::resource('strategy-sdk-distribution', 'StrategySdkDistributionController');
// SDK 版本管理
Route::get('sdk-version/meta', 'SdkVersionController@meta');
Route::resource('sdk-version', 'SdkVersionController');

// 元数据相关
Route::get('area', 'AreaController@index');
Route::get('area/grouping', 'AreaController@grouping');

// 文件上传相关
Route::post('upload', 'UploadController@uploadFile');

// 方便测试用
Route::get('test', 'TestController@index');