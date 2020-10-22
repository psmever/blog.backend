<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Front\v1\HomeController;
use App\Http\Controllers\Front\TestController;
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
// laravel 8 에서 에러 나서 변경.
// Route::get('/', [ 'as' => 'home', 'uses' => [HomeController::class, 'index'] ]);
Route::get('/', [HomeController::class, 'index'])->name('index');

// Route::get('login', ['as' => 'login', 'uses' => [HomeController::class, 'login']]);
Route::get('login', [HomeController::class, 'login'])->name('login');

Route::group(['namespace'=> 'Front', 'prefix' => 'front', 'as' => 'front.'], function () {

    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::get('index', [TestController::class, 'index'])->name('test.index');
    });

    Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
        Route::group(['prefix' => 'home', 'as' => 'home.'], function () {
            Route::get('/', [TestController::class, 'index'])->name('home.index');
            Route::get('login', [TestController::class, 'login'])->name('home.login');
        });
    });

});
