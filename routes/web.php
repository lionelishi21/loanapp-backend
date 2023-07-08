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

Route::get('/', function () {
    return view('welcome');
});

//route for installation
Route::get('install', 'Api\Install\InstallController@index');
Route::group(['prefix' => 'install'], function () {
    Route::get('start', 'Api\Install\InstallController@index');
    Route::get('requirements', 'Api\Install\InstallController@requirements');
    Route::get('permissions', 'Api\Install\InstallController@permissions');
    Route::any('database', 'Api\Install\InstallController@database');
    Route::any('installation', 'Api\Install\InstallController@installation');
    Route::get('complete', 'Api\Install\InstallController@complete');
});
