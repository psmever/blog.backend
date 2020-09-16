<?php

use Illuminate\Support\Facades\Route;

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

// uses 에러 나서 변경.
Route::get('/', [
    'as' => 'home',
    'uses' => 'Front\v1\HomeController@index'
]);

Route::get('login', [
    'as' => 'login',
    'uses' => 'Front\v1\HomeController@login'
]);

Route::group(['namespace'=> 'Front', 'prefix' => 'front', 'as' => 'front.'], function () {

    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::get('index', 'TestController@index')->name('index');
    });

    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::group(['prefix' => 'home', 'as' => 'home.'], function () {
            Route::get('/', 'HomeController@index')->name('index');
            Route::get('login', 'HomeController@login')->name('login');
        });
    });

});
