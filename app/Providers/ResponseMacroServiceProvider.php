<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Exceptions\MeteoException;
use Illuminate\Support\Facades\Response;

/**
 * Restful Response ServiceProvider
 */
class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 실패 Response
        Response::macro('error', function($statusCode = 401, $error_message = NULL) {
            $request = app(\Illuminate\Http\Request::class);
            $response = [
                'error_message' => $error_message ? $error_message : __('default.server.error'),
            ];

            if(!empty($request->get('callback'))){
                return Response()->json($response)->setCallback( $request->get('callback') );
            }else{
                return Response()->json($response, $statusCode);
            }
        });

        // 기본 성공 Response
        Response::macro('success', function ($result = null) {
            $response = new \stdClass();
            $response = [
                'message' => __('default.server.success'),
            ];

            if(!empty($result)) $response['result'] = $result;

            return Response()->json($response);
        });

        //
        Response::macro('success_only_data', function ($response = null) {
            return Response()->json($response);
        });

        // 성공 Response 데이터 없을때.
        Response::macro('success_no_content', function () {
            $response = new \stdClass();
            return Response()->json($response, 204);
        });
    }
}
