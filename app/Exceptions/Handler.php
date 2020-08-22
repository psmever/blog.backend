<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

        $logRoutename = Route::currentRouteName();
        $logRouteAction = Route::currentRouteAction();
        $current_url = url()->current();
        $logHeaderInfo = json_encode(request()->header());
        $logBodyInfo = json_encode(request()->all());

        $logBaseMessage = <<<EOF

        ID:${logid}
        Current_url:${current_url}
        RouteName:${logRoutename}
        RouteAction:${logRouteAction}
        Header: {$logHeaderInfo}
        Body: ${logBodyInfo}

        EOF;

        if ($exception instanceof \PDOException) { // ANCHOR mysql Exception report
            echo "PDOException report";
            dd($exception);
        } else if ($exception instanceof \Illuminate\Auth\AuthenticationException) { // ANCHOR AuthenticationException report
            echo "AuthenticationException report";
            dd($exception);
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { // ANCHOR NotFoundHttpException report
            Log::channel('NotFoundHttpLog')->error($logBaseMessage);
        } if ($exception instanceof \App\Exceptions\CustomException) { // ANCHOR mysql Exception report
            echo "CustomException report";
            dd($exception);
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
        // REVIEW Exception 화면에 어떻게 표시 할건지.

        // ANCHOR Custom Exception Render
        if ($exception instanceof \App\Exceptions\CustomException)  {
            // return $exception->render($request);
        } else if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) { // ANCHOR NotFoundHttpException report
            $code = 404;
            $error = [
                "error_message" => "Not Found",
                "detail" => "존재 하지 않은 요청 입니다.",
            ];
        }

        if($request->isJson()) { // ajax 요청 일떄.
            return response()->json([
                "server" => env('APP_ENV'),
                "server_time" => date("YmdHis"),
                "path" => url()->current(),
                "error" => $error
            ], $code);
        } else { // 일 반 웹 요청 일떄.
            // echo "request http";
        }

        return parent::render($request, $exception);
    }
}
