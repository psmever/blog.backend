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

Route::get('/', function () {
    return view('landing');
});

Route::get('login', function () {
    return redirect('front/v1/home/login');
})->name('login');



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
