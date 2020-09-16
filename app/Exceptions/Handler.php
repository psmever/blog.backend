<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \App\Exceptions\ClientErrorException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $logid = date('Ymdhis');
        $request_ip = request()->ip();

        $logRoutename = Route::currentRouteName();
        $logRouteAction = Route::currentRouteAction();
        $current_url = url()->current();
        $logHeaderInfo = json_encode(request()->header());
        $logBodyInfo = json_encode(request()->all());
        $method = request()->method();

        $exceptionMessage = $exception->getMessage();
        $logBaseMessage = <<<EOF

        ID:${logid}
        RequestIP:${request_ip}
        Message: ${exceptionMessage}
        Current_url:${current_url}
        RouteName:${logRoutename}
        RouteAction:${logRouteAction}
        Header: {$logHeaderInfo}
        Method: ${method}
        Body: ${logBodyInfo}

        EOF;

        if ($exception instanceof \PDOException) { // ANCHOR mysql Exception report
            Log::channel('PDOExceptionLog')->error($logBaseMessage);
        } else if ($exception instanceof \Illuminate\Auth\AuthenticationException) { // ANCHOR AuthenticationException report
            Log::channel('AuthenticationExceptionLog')->error($logBaseMessage);
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { // ANCHOR NotFoundHttpException report
            Log::channel('NotFoundHttpLog')->error($logBaseMessage);
        } else if ($exception instanceof \App\Exceptions\CustomException) { // ANCHOR mysql Exception report
            Log::channel('CustomExceptionLog')->error($logBaseMessage);
        } else if ($exception instanceof \App\Exceptions\ClientErrorException) { // ANCHOR mysql Exception report
            Log::channel('ClientExceptionLog')->error($logBaseMessage);
        } else if ($exception instanceof \App\Exceptions\ServerErrorException) { // 서버 에러 로그
            Log::channel('ServerExceptionLog')->error($logBaseMessage);
        }


        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // print_r(get_class($exception));
        $error_message = "";
        $error_code = null;

        // REVIEW Exception 화면에 어떻게 표시 할건지.
        if ($exception instanceof \App\Exceptions\CustomException) { // Custom Exception Render
            $error_code = 400;
            $error_message = $exception->getMessage();
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { // NotFoundHttpException report
            $error_code = 404;
            $error_message = __('default.exception.notfound');
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) { // MethodNotAllowedHttpException report
            $error_code = 405;
            $error_message = __('default.exception.notallowedmethod');
        } else if ($exception instanceof \App\Exceptions\ClientErrorException) { // ClientErrorException report
            $error_code = 412;
            $error_message = $exception->getMessage();
        } else if ($exception instanceof \App\Exceptions\ServerErrorException) { // ServerErrorException report
            $error_code = 500;
            $error_message = $exception->getMessage();
        } else if ($exception instanceof \Illuminate\Auth\AuthenticationException) { // AuthenticationException report
            $error_code = 401;
            $error_message = __('default.login.unauthorized');
        } else if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) { // throttle Exception
            $error_code = 429;
            $error_message = __('default.exception.throttle_exception');
        } else if ($exception instanceof \PDOException) {
            $error_code = 500;
            $error_message = __('default.exception.pdo_exception');
        } else {

            $error_code = 503;
            $error_message = [
                'error_message' => __('default.exception.error_exception'),
                'error' => $exception->getMessage()
            ];
        }

        // api 요청 일떄만 json 으로 Render.
        if($request->is('api/*')) {

            if(app()->isDownForMaintenance()) {
                return Response::error(503, $exception->getMessage() ? $exception->getMessage() : __('default.server.down'));
            } else {
                return Response::error(
                    $error_code ? $error_code : 503,
                    $error_message ? $error_message : __('default.server.error')
                );
            }
        }

        return parent::render($request, $exception);
    }
}
