<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        /**
         * 기본 Error Render Macro.
         */
        Response::macro('error', function($statusCode = 401, $error_message = NULL) {
            $request = app(\Illuminate\Http\Request::class);

            if(is_array($error_message)) {
                $response = [
                    'error' => $error_message
                ];
            } else {
                $response = [
                    'error' => [
                        'error_message' => $error_message ? $error_message : __('default.server.error')
                    ]
                ];
            }

            if(!empty($request->get('callback'))){
                return Response()->json($response)->setCallback( $request->get('callback') );
            }else{
                return Response()->json($response, $statusCode);
            }
        });

        /**
         * 기본 성공 Render Macro.
         */
        Response::macro('success', function ($result = null) {
            $response = new \stdClass();
            $response = [
                'message' => __('default.server.success'),
            ];

            if(!empty($result)) $response['result'] = $result;

            return Response()->json($response);
        });

        /**
         * 성공 메시지만 처리.
         */
        Response::macro('success_only_message', function () {
            $response = new \stdClass();
            $response = [
                'message' => __('default.server.result_success'),
            ];

            return Response()->json($response);
        });

        /**
         * 데이터만 Render Macro.
         */
        Response::macro('success_only_data', function ($response = null) {
            return Response()->json($response);
        });

        /**
         * 성공 No Contents Render Macro
         */
        Response::macro('success_no_content', function () {
            $response = new \stdClass();
            return Response()->json($response, 204);
        });
    }
}
