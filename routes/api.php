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
        Route::post('factory', 'TestController@factory')->name('factory');
        Route::post('base-user', 'TestController@base_user')->name('base.user');
    });

    Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
        Route::get('check-status', 'SystemController@checkStatus')->name('check.status'); // 서버 체크
        Route::get('check-notice', 'SystemController@checkNotice')->name('check.notice'); // 서버 공지사항 체크
        Route::get('base-data', 'SystemController@baseData')->name('base.data');  //
    });

    /**
     * Api V1 Route Group.
     */
    // FIXME 2020-09-02 21:05 auth:api middleware 정책 수립?
    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
            Route::post('login', 'AuthController@client_login')->name('login');

            Route::post('logout', 'AuthController@client_logout')->name('logout')->middleware('auth:api');
            Route::get('login-check', 'AuthController@client_login_check')->name('logincheck')->middleware('auth:api');
            Route::post('token-refresh', 'AuthController@client_token_refresh')->name('token_refresh');
        });

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        });

        Route::group(['prefix' => 'post', 'as' => 'post.'], function () {
            Route::get('/{page?}', 'PostsController@index')->name('index.paging');
            Route::post('/', 'PostsController@create')->name('create')->middleware('auth:api');
            Route::put('/{post_uuid}/publish', 'PostsController@publish')->name('publish')->middleware('auth:api');
            Route::get('/{slug_title}/detail', 'PostsController@detail')->name('detail');
            Route::get('/{post_uuid}/edit', 'PostsController@edit')->name('edit')->middleware('auth:api');
            Route::put('/{post_uuid}/update', 'PostsController@update')->name('update')->middleware('auth:api');
            Route::delete('/{post_uuid}/destroy', 'PostsController@destroy')->name('destroy')->middleware('auth:api');

            Route::post('/create-image', 'PostsController@create_image')->name('image.create')->middleware('auth:api');
        });
    });
});




