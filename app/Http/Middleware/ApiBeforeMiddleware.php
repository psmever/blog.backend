<?php

namespace App\Http\Middleware;

use App\Exceptions\ErrorException;
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
	 * @throws ErrorException
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
		
		if (!$request->wantsJson()) {
			throw new ErrorException(__('exception.wants-json'));
		}

		$exceptionRouteName = [''];
		$clientType = $request->header('client-type');

		if (!in_array(Route::currentRouteName(), $exceptionRouteName)) {
			if (empty($clientType) || !($clientType == config('appData.basic.clientCode.front') || $clientType == config('appData.basic.clientCode.front') || $clientType == config('appData.basic.clientCode.front'))) {
				throw new ErrorException(__('exception.client-type-error'));
			}
		}

		$request->LocalsMergeMacro('requestIndex', $requestIndex);

		return $next($request);
	}
}
