<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ApiAfterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle($request, Closure $next)
    // {
    //     echo ":: ApiAfterMiddleware :: ";
    //     return $next($request);
    // }

    // public function terminate($request, $response)
	// {
    //     // TODO:: ApiAfterMiddleware 응답 끝났을떄.
    //     echo ":: terminate ::";
    //     return  ":: terminate ::";
    // }

    public function handle($request, Closure $next)
    {
	    $response = $next($request);

	    return $response;
    }

    public function terminate($request, $response)
    {
        // Store the session data...
        Log::debug('ApiAfterMiddleware terminate.');
    }
}
