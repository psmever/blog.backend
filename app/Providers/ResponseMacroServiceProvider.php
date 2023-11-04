<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * 데이터 없을때
         */
        Response::macro('successNoContent', function () {
            $response = new \stdClass();
            return Response()->json($response, 204);
        });

        /**
         * 정상 처리
         */
        Response::macro('success', function ($paramsData = null, $paramsStatusCode = null) {
            $statusCode = $paramsStatusCode ? $paramsStatusCode : 200;
            $responseData  = $paramsData ? $paramsData : [
                'message' => __('response.success'),
            ];

            return Response()->json($responseData, $statusCode);
        });

        /**
         * 클라이언트 에러
         */
        Response::macro('clientError', function ($message = null) {
            return Response()->json([
                'message' => $message ? $message : __('response.client-error')
            ], 400);
        });

        /**
         * 서버 에러
         */
        Response::macro('serverError', function ($message = null) {
            return Response()->json([
                'message' => $message ? $message : __('response.server-error')
            ], 500);
        });
    }
}
