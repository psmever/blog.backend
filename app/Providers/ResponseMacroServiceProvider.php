<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use stdClass;

class ResponseMacroServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(Request $request): void
	{
		/**
		 * 성공 No Contents Render Macro
		 */
		Response::macro('SuccessNoContentMacro', function () {
			$response = new stdClass();
			return Response()->json($response, 204);
		});

		/**
		 * 결과 커스텀 하게 사용.
		 */
		Response::macro('SuccessMacro', function ($paramData = NULL, $message = null, $statusCode = 200) use ($request) {

			if (!$paramData) {
				$response = [
					'message' => $message ?: __('response.success')
				];
			} else {
				$response = $paramData;
			}

			return Response()->json($response, $statusCode);
		});

		/**
		 * 기본 Error Render Macro.
		 */
		Response::macro('ErrorMacro', function ($message = null, $errors = NULL, $statusCode = 400) use ($request) {

			$response = [
				'message' => $message ?: __('response.error')
			];

			if (App::environment(['local', 'development']) && $errors) {
				$response['error'] = $errors;
			}

			if ($request->wantsJson()) {
				return Response()->json($response, $statusCode);
			}

			return Response(implode("\n", $response), $statusCode);
		});
	}
}
