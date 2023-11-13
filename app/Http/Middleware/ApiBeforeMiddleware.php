<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ApiBeforeMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$requestIndex = date('YmdHis') . '-' . mt_rand();

		// request log
		$environment = env('APP_ENV');
		$request_ip = request()->ip();
		$current_url = url()->current();
		$logRouteAction = Route::currentRouteAction();
		$logRouteName = Route::currentRouteName();
		$method = request()->method();
		$logHeaderInfo = json_encode(request()->header());
		$logBodyInfo = json_encode(request()->all());

		$log = <<<EOF

REQUEST_INDEX: $requestIndex
ENV: $environment
RequestIP: $request_ip
Current_url: $current_url
RouteAction: $logRouteAction
RouteName: $logRouteName
Method: $method
Header: $logHeaderInfo
Body: $logBodyInfo

EOF;
		Log::channel('request-log')->info($log);

		$request->LocalsMergeMacro('requestIndex', $requestIndex);

		return $next($request);
	}
}
