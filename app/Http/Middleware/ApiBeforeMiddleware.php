<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
		$request->LocalsMergeMacro('requestIndex', date('YmdHis') . '-' . mt_rand());
		return $next($request);
	}
}
