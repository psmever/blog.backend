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

use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SystemController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('base-data', [SystemController::class, 'index']);
    Route::middleware(['auth:sanctum', 'token.expiry'])->group(function () {
        Route::post('posts/uuid', [PostController::class, 'issueUuid']);
        Route::post('posts', [PostController::class, 'store']);
        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{uuid}', [PostController::class, 'show'])->whereUuid('uuid');
        Route::post('posts/{uuid}/images', [PostController::class, 'uploadImage'])->whereUuid('uuid');
        Route::post('posts/{uuid}/cover-image', [PostController::class, 'setCoverImage'])->whereUuid('uuid');
        Route::post('posts/{uuid}/save', [PostController::class, 'save'])->whereUuid('uuid');
        Route::post('posts/{uuid}/publish', [PostController::class, 'publish'])->whereUuid('uuid');
    });
});
