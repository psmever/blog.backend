<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiBaseController extends Controller
{
    /**
     * ✅ 성공 응답
     */
    protected function success(mixed $data = null, string $message = 'ok', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * ⚠️ 클라이언트 에러 (400대)
     */
    protected function error(string $message = 'Bad Request', int $status = Response::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    /**
     * 🔐 인증 실패
     */
    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->error($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * ❌ 서버 내부 에러
     */
    protected function fail(string $message = 'Internal Server Error', mixed $errors = null): JsonResponse
    {
        return $this->error($message, Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
    }
}
