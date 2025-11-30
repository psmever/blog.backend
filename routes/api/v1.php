<?php

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
|
| 모든 API v1 요청은 /api/v1/... 형태로 들어옵니다.
| 인증, 사용자, 헬스체크 등 API 버전별 라우트를 정의합니다.
|
*/

use App\Http\Controllers\Api\V1\SystemController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('base-data', [SystemController::class, 'index']);
});
