<?php

use App\Http\Middleware\ValidateClientType;
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
        $middleware->alias([
            'token.expiry' => \App\Http\Middleware\CheckTokenExpiry::class,
        ]);

        $middleware->appendToGroup('api', ValidateClientType::class);
    })
    ->withCommands([
        \App\Console\Commands\ExportPostmanCollection::class,
        \App\Console\Commands\PruneExpiredTokens::class,
        \App\Console\Commands\RepairLocalMigrationRegistry::class,
        \App\Console\Commands\TruncatePostTables::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        // 헬퍼: 이 요청이 API인지?
        $isApi = fn (Request $r) => $r->is('api/*') || $r->expectsJson();

        // 422 ValidationException
        $exceptions->render(function (ValidationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json([
                'message' => '요청값이 올바르지 않습니다.',
                'errors' => $e->errors(),
            ], 422);
        });

        // 401 인증 실패
        $exceptions->render(function (AuthenticationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => '인증 정보가 유효하지 않습니다.'], 401);
        });

        // 403 권한 거부
        $exceptions->render(function (AuthorizationException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => '해당 작업을 수행할 권한이 없습니다.'], 403);
        });

        // 404: 모델/페이지 미존재
        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => '요청하신 경로를 찾을 수 없습니다.'], 404);
        });

        // 405: 메서드 불가
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => '지원하지 않는 요청 방식입니다.'], 405);
        });

        // 429: 레이트 리밋
        $exceptions->render(function (ThrottleRequestsException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            return response()->json(['message' => '요청이 너무 많습니다. 잠시 후 다시 시도해 주세요.'], 429);
        });

        // DB 예외(메시지는 운영에서 숨김)
        $exceptions->render(function (QueryException $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }
            $msg = app()->isProduction()
                ? '데이터베이스 처리 중 오류가 발생했습니다.'
                : $e->getMessage();

            return response()->json(['message' => $msg], 500);
        });

        // 임의의 HttpException (status 갖고 있음)
        $exceptions->render(function (HttpExceptionInterface $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }
            $status = $e->getStatusCode();
            $msg = $e->getMessage() ?: 'HTTP Error';

            return response()->json(['message' => $msg ?: '요청 처리 중 오류가 발생했습니다.'], $status);
        });

        // 최종 fallback (500)
        $exceptions->render(function (Throwable $e, Request $r) use ($isApi) {
            if (! $isApi($r)) {
                return null;
            }

            // 개발환경은 메시지/트레이스 노출, 운영은 메시지 최소화
            if (app()->isProduction()) {
                return response()->json(['message' => '서버 내부 오류가 발생했습니다.'], 500);
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
