<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthController;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
|
| 모든 API v1 요청은 /api/v1/... 형태로 들어옵니다.
| 인증, 사용자, 헬스체크 등 API 버전별 라우트를 정의합니다.
|
*/

Route::get('/health', [HealthController::class, 'index']);

// 인증 관련 (예시)
// Route::prefix('auth')->group(function () {
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);
// });

// 보호된 라우트 (예: 사용자 정보)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('user/me', [UserController::class, 'me']);
// });
