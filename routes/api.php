<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ManageController;
use App\Http\Controllers\Api\V1\MediaController;
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

// 테스트용
Route::group(['prefix' => 'test', 'as' => 'test.'], function () {
	Route::controller(TestController::class)->group(function () {
		Route::get('/default', 'default')->name('default');
		Route::post('/test-user-create', 'testUserCreate')->name('test.user.create');
		Route::get('/success-test', 'successTest')->name('success.test');
		Route::get('/error-test', 'errorTest')->name('error.test');
	});
});

// 시스템
Route::group(['prefix' => 'system', 'as' => 'system.'], function () {
	Route::controller(SystemController::class)->group(function () {
		Route::get('/status', 'SystemStatus')->name('status');
		Route::get('/notice', 'SystemNotice')->name('notice');
		Route::get('/app-data', 'SystemAppData')->name('app.data');
	});
});

// version 1
Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
	// 인증 처리
	Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
		Route::controller(AuthController::class)->group(function () {
			Route::post('/login', 'Login')->name('login');
			Route::delete('/logout', 'Logout')->name('logout')->middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value]);
			Route::get('/token-info', 'TokenInfo')->name('token.info')->middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value]);
			Route::get('/refresh-token', 'RefreshToken')->name('refresh.token')->middleware(['auth:sanctum', 'ability:' . TokenAbility::REFRESH_TOKEN->value]);
		});
	});

	// media
	Route::group(['prefix' => 'media', 'as' => 'media.'], function () {
		Route::controller(MediaController::class)->group(function () {
			Route::post('/create', 'Create')->name('create')->middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value]);
		});
	});

	// 관리 관련 api
	Route::group(['prefix' => 'manage', 'as' => 'manage.'], function () {
		Route::controller(ManageController:: class)->group(function () {
			Route::post('/post-create', 'PostCreate')->name('post.create')->middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value]);
			Route::get('/post/{uid}/info', 'PostInfo')->name('post.create');
		});
	});
});
