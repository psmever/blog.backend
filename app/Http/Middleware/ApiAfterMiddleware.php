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

        // FIXME: Local 버전에서만 Response 관련 클라이언트 정보를 기록,
        // Prod 버전에서는 어떻게 할것인지 생각해봐야.....
        // Response Payload 에 대한 기록은 어떻게 할것인지 생각해봐야..
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
