<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HealthController;
use App\Exceptions\ApiException;

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
Route::prefix('v1')->group(function () {
    require __DIR__ . '/v1.php';
});

Route::get('/health', [HealthController::class, 'index']);
Route::get('/_demo/ok', fn () => response()->json(['message' => 'ok', 'data' => ['at' => now()]]));
Route::get('/_demo/boom', fn () => throw new \Exception('boom!'));
Route::get('/_demo/api-ex', fn () => throw new ApiException('Bad thing', 422, ['field' => ['wrong']]));
Route::post('/_demo/validate', function (\Illuminate\Http\Request $r) {
    $r->validate(['title' => ['required','min:3']]);
    return response()->json(['message' => 'ok']);
});
