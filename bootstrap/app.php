<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // ✅ Web Routes
        web: [
            __DIR__.'/../routes/web/web.php',
            __DIR__.'/../routes/web/admin.php',
        ],

        // ✅ API Routes
        api: [
            __DIR__.'/../routes/api/api.php',
        ],

        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 헬퍼: 이 요청이 API인지?
        $isApi = fn (Request $r) => $r->is('api/*') || $r->expectsJson();

        // 422 ValidationException
        $exceptions->render(function (ValidationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        });

        // 401 인증 실패
        $exceptions->render(function (AuthenticationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => 'Unauthorized'], 401);
        });

        // 403 권한 거부
        $exceptions->render(function (AuthorizationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => 'Forbidden'], 403);
        });

        // 404: 모델/페이지 미존재
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => 'Not Found'], 404);
        });

        // 405: 메서드 불가
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => 'Method Not Allowed'], 405);
        });

        // 429: 레이트 리밋
        $exceptions->render(function (ThrottleRequestsException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => 'Too Many Requests'], 429);
        });

        // DB 예외(메시지는 운영에서 숨김)
        $exceptions->render(function (QueryException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }
            $msg = app()->isProduction() ? 'Database error' : $e->getMessage();

            return response()->json(['message' => $msg], 500);
        });

        // 임의의 HttpException (status 갖고 있음)
        $exceptions->render(function (HttpExceptionInterface $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }
            $status = $e->getStatusCode();
            $msg = $e->getMessage() ?: 'HTTP Error';

            return response()->json(['message' => $msg], $status);
        });

        // 최종 fallback (500)
        $exceptions->render(function (Throwable $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            // 개발환경은 메시지/트레이스 노출, 운영은 메시지 최소화
            if (app()->isProduction()) {
                return response()->json(['message' => 'Server error'], 500);
            }

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [
                    'exception' => class_basename($e),
                    'trace' => collect($e->getTrace())->take(5), // 너무 길지 않게 상위 5개만
                ],
            ], 500);
        });

        // (선택) 리포터: 특정 예외만 모니터링/슬랙 전송 등
        // $exceptions->report(function (Throwable $e) {
        //     // report($e); // sentry/bugsnag 등
        // });
    })
    ->create();
