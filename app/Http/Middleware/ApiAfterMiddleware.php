<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAfterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response) : void
    {
        // 종료시?
    }
}
