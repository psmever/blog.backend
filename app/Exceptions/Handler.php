<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
        // ANCHOR mysql Exception report
        if ($exception instanceof \PDOException) {
            echo "PDOException report";
        }

        // ANCHOR AuthenticationException report
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            echo "AuthenticationException report";
        }

        // ANCHOR NotFoundHttpException report
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            echo "NotFoundHttpException report";
        }

        // ANCHOR mysql Exception report
        if ($exception instanceof \App\Exceptions\CustomException) {
            echo "CustomException report";
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
            return $exception->render($request);
        }

        return parent::render($request, $exception);
    }
}
