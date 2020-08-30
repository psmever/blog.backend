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
        $logid = date('Ymdhis');
        $request_ip = request()->ip();

        // TODO response 끝났을때 로그를 어떻게?
        if(env('APP_ENV') == "local") {
            $logRoutename = Route::currentRouteName();
            $logRouteAction = Route::currentRouteAction();

            $current_url = url()->current();
            $logHeaderInfo = json_encode($request->header());
            $logBodyInfo = json_encode($request->all());
            $method = $request->method();

            $logMessage = <<<EOF

            ID: ${logid}
            RequestIP: ${request_ip}
            Current_url: ${current_url}
            RouteName: ${logRoutename}
            RouteAction: ${logRouteAction}
            Header: ${logHeaderInfo}
            Method: ${method}
            Body: ${logBodyInfo}

            EOF;
            Log::channel('ApiTerminatelog')->debug($logMessage);
        }
    }
}
