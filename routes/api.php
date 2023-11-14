<?php

use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
	Route::controller(TestController::class)->group(function () {
		Route::get('/default', 'default')->name('default');
		Route::get('/success-test', 'successTest')->name('success.test');
		Route::get('/error-test', 'errorTest')->name('error.test');
	});
});

Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
	Route::controller(SystemController::class)->group(function () {
		Route::get('/status', 'SystemStatus')->name('status');
		Route::get('/notice', 'SystemNotice')->name('notice');
		Route::get('/app-data', 'SystemAppData')->name('app.data');
	});
});
