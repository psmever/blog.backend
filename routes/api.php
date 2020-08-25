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

Route::group(['as' => 'api.'], function () {
    /**
     * Api Test 용 컨트롤러.
     */
    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::post('index', 'TestController@index')->name('index');
        Route::post('slack', 'TestController@slack')->name('slack');
    });

    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::post('deploy', 'SystemController@deploy')->name('deploy');
        Route::get('check/status', 'SystemController@check_status')->name('check_status');
        Route::get('check/notice', 'SystemController@check_notice')->name('check_notice');
        Route::get('base_data', 'SystemController@base_data')->name('base_data');
    });

    /**
     * Api V1 Route Group.
     */
    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'api.v1.'], function () {

    });
});




