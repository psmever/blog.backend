<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * ✅ 성공 응답
     */
    protected function responseSuccess(
        mixed $data = null,
        string $message = 'ok',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'meta' => [
                'status' => $status,
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }

    /**
     * ⚠️ 일반 오류
     */
    protected function responseError(
        string $message = 'Bad Request',
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'status' => $status,
                'timestamp' => now()->toISOString(),
            ],
        ], $status);
    }

    /**
     * 🔐 인증 실패
     */
    protected function responseUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 🚫 접근 거부
     */
    protected function responseForbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * 🔍 리소스 없음
     */
    protected function responseNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * 💥 서버 내부 오류
     */
    protected function responseFail(
        string $message = 'Internal Server Error',
        mixed $errors = null
    ): JsonResponse {
        return $this->responseError($message, Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
    }

    /**
     * 📄 페이지네이션 응답
     */
    protected function responsePaginated($paginator, string $message = 'ok'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'status' => Response::HTTP_OK,
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'timestamp' => now()->toISOString(),
            ],
        ], Response::HTTP_OK);
    }
}
