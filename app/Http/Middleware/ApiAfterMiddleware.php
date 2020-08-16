<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ApiAfterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	    $response = $next($request);

	    return $response;
    }

    public function terminate($request, $response)
    {
        // ANCHOR Teminate Log
        $logid = date('Ymdhis');

        $logRoutename = Route::currentRouteName();
        $logRouteAction = Route::currentRouteAction();

        $current_url = url()->current();
        $logHeaderInfo = json_encode($request->header());
        $logBodyInfo = json_encode($request->all());

        $logMessage = <<<EOF
ID:${logid}
Current_url:${current_url}
RouteName:${logRoutename}
RouteAction:${logRouteAction}
Header: {$logHeaderInfo}
Body: ${logBodyInfo}

EOF;
        Log::channel('ApiTerminatelog')->debug($logMessage);
    }
}
