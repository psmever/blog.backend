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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['prefix' => 'util', 'as' => 'util.'], function () {
//     Route::post('deploy', 'UtilController@deploy')->name('deploy'); // 임시 배포 컨트롤러.
// });

Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::post('test', 'TestController@test')->name('test');

});
