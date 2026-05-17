<?php

namespace App\Traits;

use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * ✅ 성공 응답
     */
    protected function responseSuccess(
        mixed $data = null,
        string $message = '정상 처리되었습니다',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => [
                'status' => $status,
                'timestamp' => $this->currentTimestamp(),
            ],
        ], $status);
    }

    /**
     * ⚠️ 일반 오류
     */
    protected function responseError(
        string $message = '잘못된 요청입니다',
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
            'meta' => [
                'status' => $status,
                'timestamp' => $this->currentTimestamp(),
            ],
        ], $status);
    }

    /**
     * 🔐 인증 실패
     */
    protected function responseUnauthorized(string $message = '인증이 필요합니다'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * 🚫 접근 거부
     */
    protected function responseForbidden(string $message = '접근이 거부되었습니다'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * 🔍 리소스 없음
     */
    protected function responseNotFound(string $message = '요청한 리소스를 찾을 수 없습니다'): JsonResponse
    {
        return $this->responseError($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * 💥 서버 내부 오류
     */
    protected function responseFail(
        string $message = '서버 내부 오류가 발생했습니다',
        mixed $errors = null
    ): JsonResponse {
        return $this->responseError($message, Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
    }

    /**
     * 📄 페이지네이션 응답
     */
    protected function responsePaginated($paginator, string $message = '정상 처리되었습니다'): JsonResponse
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
                'timestamp' => $this->currentTimestamp(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @param  array<int, mixed>  $items
     */
    protected function responseCursorPaginated(
        array $items,
        int $limit,
        ?string $nextCursor,
        bool $hasMore,
        string $message = '정상 처리되었습니다'
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'data' => $items,
            'meta' => [
                'status' => Response::HTTP_OK,
                'timestamp' => $this->currentTimestamp(),
                'limit' => $limit,
                'next_cursor' => $nextCursor,
                'has_more' => $hasMore,
            ],
        ], Response::HTTP_OK);
    }

    protected function currentTimestamp(): string
    {
        return $this->formatDateTimeForResponse(now()) ?? now()->toDateTimeString();
    }

    protected function formatDateTimeForResponse(?CarbonInterface $dateTime): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        try {
            return $dateTime->copy()->setTimezone($this->responseTimezone())->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return $dateTime->toDateTimeString();
        }
    }

    protected function responseTimezone(): \DateTimeZone
    {
        $timezone = config('app.display_timezone', 'Asia/Seoul');
        $timezone = is_string($timezone) && $timezone !== '' ? $timezone : 'Asia/Seoul';

        try {
            return new \DateTimeZone($timezone);
        } catch (\Exception) {
            return new \DateTimeZone('UTC');
        }
    }
}
