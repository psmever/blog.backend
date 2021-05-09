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
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['as' => 'api.'], function () {
    /**
     * Api Test 용 컨트롤러.
     */
    Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
        Route::post('default', [TestController::class, 'default'])->name('default');
    });
});
