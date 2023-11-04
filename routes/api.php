<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;

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
        Route::get('/success-no-content', 'successNoContent')->name('success-no-content');
        Route::get('/success', 'success')->name('success');
        Route::get('/client-error', 'clientError')->name('client-error');
        Route::get('/server-error', 'serverError')->name('server-error');
	});
});
