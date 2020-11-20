<?php
/**
 * 渠道方路由
 */

// Authentication Routes...
// 仅支持Login/Logout
Route::get('login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', '\App\Http\Controllers\Auth\LoginController@login');
Route::post('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('password/update', 'Auth\UpdatePasswordController@showUpdateForm')->name('password.update');
Route::post('password/update', 'Auth\UpdatePasswordController@update');

// 重定向
Route::redirect('/', '/report-full', 302);
Route::redirect('/home', '/report-full', 302)->name('home');

// Report Full
Route::get('/report-full', 'ReportController@index');

// Publisher
Route::post('publisher/activate', 'PublisherController@activate'); // 激活用户
Route::get('publisher/check-exist', 'PublisherController@checkExist');
Route::get('publisher/login', 'PublisherController@login');
Route::get('publisher/allow-firms/{id}/edit', 'PublisherController@editAllowFirms');
Route::put('publisher/allow-firms/{id}', 'PublisherController@updateAllowFirms');
Route::get('publisher/login', 'PublisherController@login');
Route::put('publisher/update-sub-publisher', 'PublisherController@updateSubPublisher');
Route::resource('publisher', 'PublisherController');