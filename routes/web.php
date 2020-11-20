<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::redirect('password/update', 'redirect?dest=account/password')->name('password.update');
Route::redirect('email-signature', 'redirect?dest=account/email-signature')->name('email.signature');

// 用户&角色管理
//Route::post('users/token', 'UserController@getToken');
//Route::resource('users', 'UserController');
Route::redirect('users', 'redirect?dest=admin/manage-user');
//Route::resource('roles', 'RoleController');
Route::redirect('roles', 'redirect?dest=admin/roles');
//Route::resource('permissions', 'PermissionController');
Route::redirect('permissions', 'redirect?dest=admin/permissions');

// 重定向
Route::redirect('/', '/report-full', 302);
Route::redirect('/home', '/report-full', 302)->name('home');

// Publisher
Route::post('publisher/activate', 'PublisherController@activate');//激活用户
Route::get('publisher/check-exist', 'PublisherController@checkExist');
Route::get('publisher/allow-firms/{id}/edit', 'PublisherController@editAllowFirms');
Route::put('publisher/allow-firms/{id}', 'PublisherController@updateAllowFirms');
Route::put('publisher/update-sub-publisher', 'PublisherController@updateSubPublisher');
Route::get('publisher/login', 'PublisherController@login');
//Route::resource('publisher', 'PublisherController');
Route::redirect('publisher', 'redirect?dest=publishers/manage-publisher');

// Publisher Group
//Route::resource('publisher-group', 'PublisherGroupController');

Route::redirect('app', 'redirect?dest=publishers/manage-app');
Route::redirect('app-term', 'redirect?dest=publishers/manage-app-label');

//Route::resource('unit-change-log', 'UnitChangeLogController');
Route::redirect('unit-change-log', 'redirect?dest=publishers/change-log');

//Route::resource('placement', 'PlacementController');
Route::redirect('placement', 'redirect?dest=publishers/manage-placement');

//Route::resource('strategy-app', 'StrategyAppController');
Route::redirect('strategy-app', 'redirect?dest=strategy/app');

Route::resource('strategy-app-firm', 'StrategyAppFirmController');
Route::resource('strategy-app-firm-switch', 'StrategyAppFirmSwitchController');

Route::redirect('strategy-placement', 'redirect?dest=/strategy/placement-strategy');

Route::resource('strategy-placement-firm', 'StrategyPlacementFirmController');

Route::redirect('report-unit-log', 'redirect?dest=publishers/manage-report-api');
Route::redirect('network-crawl', 'redirect?dest=publishers/report-api-rules');

// Full Report
Route::get('/full-report', 'ReportController@index'); // 已下线
Route::get('/full-report-v2', 'ReportController@index'); // 已下线
Route::get('report-full', 'ReportFullController@index'); // 最新版本

// Chart Report
Route::get('/chart-report', 'ChartReportController@index');
Route::get('/chart-report-v2', 'ChartReportController@index');

// Tc Report
Route::get('/report-tc', 'ReportTcController@index')->name('report-tc');

// 报表指标自定义
Route::post('metrics-setting/full-report', 'MetricsSettingController@fullReport');

// Revenue Deduction
Route::redirect('revenue-deduction', 'redirect?dest=publishers/revenue-deduction-main-account');
// Revenue Deduction Sub Account
Route::redirect('revenue-deduction-sub-account', 'redirect?dest=publishers/revenue-deduction-sub-account');

// Firm Manage
Route::redirect('network-firm', 'redirect?dest=publishers/manage-firm');
Route::redirect('firm-adapter', 'redirect?dest=publishers/manage-firm-adapter');

// 扣量规则
Route::redirect('/manage-impression', 'redirect?dest=publishers/manage-impression');
Route::redirect('/manage-fill-rate', 'redirect?dest=publishers/manage-fill-rate');

//Route::resource('metrics-report', 'MetricsReportController');
Route::redirect('metrics-report', 'redirect?dest=publishers/manage-report-metrics');

// 报表导入数据
Route::redirect('report-import', 'redirect?dest=/publishers/upload-network-report');

//Route::get('upload-rules/copy-one/{src_id}}', 'UploadRulesController@copyOne')->name("upload-rules.copy");
//Route::resource('upload-rules', 'UploadRulesController');
Route::redirect('upload-rules', 'redirect?dest=strategy/upload-rules');

Route::get('tc-upload-rule/copy-one/{src_id}}', 'TcUploadRulesController@copyOne')->name("tc-upload-rule.copy");
Route::resource('tc-upload-rule', 'TcUploadRulesController');

Route::resource('tc-mapping-rule', 'TcMappingRuleController');

Route::redirect('tc-strategy', 'redirect?dest=/strategy/tc-strategy');

Route::redirect('strategy-placement-my-offer', 'redirect?dest=/strategy/myoffer-strategy');

Route::redirect('strategy-plugin', 'redirect?dest=/strategy/plugin-strategy');

// Strategy Firm
Route::redirect('strategy-firm', 'redirect?dest=/strategy/firm-strategy');

// Strategy Sdk Distribution
//Route::resource('strategy-sdk-distribution', 'StrategySdkDistributionController');
Route::redirect('strategy-sdk-distribution', 'redirect?dest=strategy/sdk-distribution');

Route::redirect('posts', 'redirect?dest=contents/news-events');
Route::redirect('posts-term', 'redirect?dest=contents/categories-tags');
Route::redirect('contact', 'redirect?dest=contents/contacts');
Route::redirect('subscribers', 'redirect?dest=contents/subscribers');

// 新旧系统跳转
Route::get('redirect', 'RedirectController@redirect');