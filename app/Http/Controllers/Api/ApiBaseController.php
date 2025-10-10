<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class ApiBaseController extends BaseController
{
/**
     * 성공 응답 (HTTP 코드 기반)
     */
    protected function success($data = null, string $message = '정상 처리 하였습니다.', int $code = 200): JsonResponse
    {
        $response = ['message' => $message];
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * 에러 응답
     */
    protected function error(string $message = '에러가 발생했습니다.', int $code = 400, $errors = null): JsonResponse
    {
        $response = ['message' => $message];
        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
