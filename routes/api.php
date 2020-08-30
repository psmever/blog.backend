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

        Route::get('server-check', 'SystemController@serverCheck')->name('server.check'); // 서버 체크용 API

        Route::post('deploy', 'SystemController@deploy')->name('deploy');
        Route::get('check-status', 'SystemController@checkStatus')->name('check.status');
        Route::get('check-notice', 'SystemController@checkNotice')->name('check.notice');
        Route::get('base-data', 'SystemController@baseData')->name('base.data');
    });

    /**
     * Api V1 Route Group.
     */
    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
            Route::post('login', 'AuthController@client_login')->name('login');

            Route::post('logout', 'AuthController@client_logout')->name('logout')->middleware('auth:api');
            Route::post('login-check', 'AuthController@client_login_check')->name('logincheck')->middleware('auth:api');
        });

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        });
    });
});




