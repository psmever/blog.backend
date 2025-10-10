<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // ✅ Web Routes
        web: [
            __DIR__ . '/../routes/web/web.php',
            __DIR__ . '/../routes/web/admin.php',
        ],

        // ✅ API Routes
        api: [
            __DIR__ . '/../routes/api/api.php',
        ],

        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
