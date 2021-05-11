<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PDOException;
use Throwable;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

//use App\Exceptions\CustomException;
//use App\Exceptions\ClientErrorException;
//use App\Exceptions\ServerErrorException;
//use App\Exceptions\ForbiddenErrorException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });


        /**
         * CustomException
         */

        $this->renderable(function (CustomException $e) {

            $error_message = $e->getMessage() ?: __('default.server.error');

            Log::channel('CustomExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(400, $error_message);
        });

        /**
         * NotFoundHttpException
         */
        $this->renderable(function (NotFoundHttpException $e) {

            $error_message = $e->getMessage() ?: __('default.exception.notfound');

            Log::channel('NotFoundHttpLog')->error($this->getLoggerMessage($error_message));

            return Response::error(404, $error_message);
        });

        /**
         * MethodNotAllowedHttpException
         */
        $this->renderable(function (MethodNotAllowedHttpException $e) {

            $error_message = $e->getMessage() ?: __('default.exception.notallowedmethod');

            Log::channel('CustomExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(405, $error_message);
        });

        /**
         * ClientErrorException
         */
        $this->renderable(function (ClientErrorException $e) {

            $error_message = $e->getMessage();

            Log::channel('ClientExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(412, $$error_message);
        });

        /**
         * ServerErrorException
         */
        $this->renderable(function (ServerErrorException $e) {

            $error_message = $e->getMessage();

            Log::channel('ServerExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(500, $error_message);
        });

        /**
         * ForbiddenErrorException
         */
        $this->renderable(function (ForbiddenErrorException $e) {

            $error_message = ($e->getMessage()) ?: __('default.exception.forbidden_error_exception');

            Log::channel('CustomExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(430, $error_message);
        });

        /**
         * AuthenticationException
         */
        $this->renderable(function (AuthenticationException $e) {

            $error_message = ($e->getMessage()) ?: __('default.login.unauthorized');

            Log::channel('authenticationlog')->error($this->getLoggerMessage($error_message));

            return Response::error(401, $error_message);
        });

        /**
         * ThrottleRequestsException
         */
        $this->renderable(function (ThrottleRequestsException $e) {

            $error_message = ($e->getMessage()) ?: __('default.exception.throttle_exception');

            Log::channel('CustomExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(429, $error_message);
        });

        /**
         * PDOException
         */
        $this->renderable(function (PDOException $e) {

            $error_message = ($e->getMessage()) ?: __('default.exception.pdo_exception');

            Log::channel('PDOExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(
                500,
                __('default.exception.pdo_exception'),
            );
        });

        /**
         * ModelNotFoundException
         */
        $this->renderable(function (ModelNotFoundException $e) {
            return Response::error(
                404,
                $e->getMessage() ?: __('default.exception.model_not_found_exception'),
            );
        });

        /**
         * EtcException
         */
        $this->renderable(function (Throwable $e) {

            $error_message = 'error_message : ' . __('default.exception.error_exception');
            $error_message .= 'error' .$e->getMessage();

            Log::channel('CustomExceptionLog')->error($this->getLoggerMessage($error_message));

            return Response::error(
                503,
                [
                    'error_message' => __('default.exception.error_exception'),
                    'error' => $e->getMessage()
                ]
            );
        });

    }

    /**
     * 로그할 메시지 생성.
     *
     * @param string $logMessage
     * @return string
     */
    function getLoggerMessage(string $logMessage = "") : string
    {
        $logID = Carbon::now()->format('YmdHis');
        $request_ip = request()->ip();

        $logRouteName = Route::currentRouteName();
        $logRouteAction = Route::currentRouteAction();
        $current_url = url()->current();
        $logHeaderInfo = json_encode(request()->header());
        $logBodyInfo = json_encode(request()->all());
        $method = request()->method();

        return <<<EOF

        ID: $logID
        RequestIP: $request_ip
        Message: $logMessage
        Current_url: $current_url
        RouteName: $logRouteName
        RouteAction: $logRouteAction
        Header: $logHeaderInfo
        Method: $method
        Body: $logBodyInfo

EOF;

    }
}
