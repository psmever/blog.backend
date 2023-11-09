<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAfterMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Closure(Request): (Response) $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		return $next($request);
	}

	/**
	 * @param $request
	 * @param $response
	 * @return void
	 */
	public function terminate($request, $response): void
	{
		// 종료시?
	}
}
