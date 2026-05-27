<?php

use App\Exceptions\ApiException;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (root)
|--------------------------------------------------------------------------
|
| 여기서는 API의 버전별 라우트를 묶습니다.
| 실제 엔드포인트는 /api/v1/... 형태로 구성됩니다.
|
*/

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
|
| 모든 API v1 요청은 /api/v1/... 형태로 들어옵니다.
| 인증, 사용자, 헬스체크 등 API 버전별 라우트를 정의합니다.
|
*/

require __DIR__.'/v1.php';

Route::get('/health', [HealthController::class, 'index']);

if (app()->environment(['local', 'development', 'testing'])) {
    Route::get('/_demo/ok', fn () => response()->json(['message' => 'ok', 'data' => ['at' => now()]]));
    Route::get('/_demo/boom', fn () => throw new Exception('boom!'));
    Route::get('/_demo/api-ex', fn () => throw new ApiException('Bad thing', 422, ['field' => ['wrong']]));
    Route::post('/_demo/validate', function (Request $r) {
        $r->validate(['title' => ['required', 'min:3']]);

        return response()->json(['message' => 'ok']);
    });
}

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| 인증 관련 라우트는 여기서 정의합니다.
| 로그인, 로그아웃, 토큰 갱신 등의 엔드포인트를 포함합니다.
|--------------------------------------------------------------------------*/
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'token.expiry'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout/all', [AuthController::class, 'logoutAll']);
    });
});
