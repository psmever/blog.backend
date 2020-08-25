<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Closure;

class ApiBeforeMiddleware
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
        // NOTE 헤더 클라이언트 체크.
        $clientType = $request->header('request-client-type');

        if(Route::currentRouteName() != "api.system.deploy") {
            if(empty($clientType) || !($clientType == env('FRONT_CLIENT_CODE') || $clientType == env('IOS_CLIENT_CODE') || $clientType == env('ANDROID_CLIENT_CODE'))) {
                throw new \App\Exceptions\ClientErrorException(__('default.exception.clienttype'));
            }
        }
        return $next($request);
    }
}
