<?php

namespace App\Exceptions;

use http\Exception\BadMethodCallException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use PDOException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;


class Handler extends ExceptionHandler
{
	/**
	 * The list of the inputs that are never flashed to the session on validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void
	{
		$this->reportable(function (Throwable $e) {

		});

		$this->renderable(function (ClientErrorException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.client-error'), $this->generateError($e), 400);
		});

		$this->renderable(function (NotFoundHttpException $e) {
			return Response::ErrorMacro(__('exception.not-found-http'), $this->generateError($e), 404);
		});

		$this->renderable(function (MethodNotAllowedHttpException $e) {
			return Response::ErrorMacro(__('exception.method-not-allowed-http'), $this->generateError($e), 405);
		});

		$this->renderable(function (ServerErrorException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.server-error'), $this->generateError($e), 500);
		});

		$this->renderable(function (ErrorException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.error'), $this->generateError($e));
		});

		$this->renderable(function (PDOException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.server-pdo-error'), $this->generateError($e), 500);
		});

		$this->renderable(function (ForbiddenErrorException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.forbidden-error'), $this->generateError($e), 403);
		});

		$this->renderable(function (AuthenticationException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.authentication'), $this->generateError($e), 401);
		});

		$this->renderable(function (ThrottleRequestsException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.throttle-exception'), $this->generateError($e), 429);
		});

		$this->renderable(function (BadMethodCallException $e) {
			return Response::ErrorMacro($e->getMessage() ?: __('exception.bad-method-call'), $this->generateError($e), 404);
		});

	}

	/**
	 * Throwable to array
	 * @param Throwable $e
	 * @return array
	 */
	public function generateError(Throwable $e): array
	{
		return [
			"message" => $e->getMessage(),
			"code" => $e->getCode(),
			"file" => $e->getFile(),
			"line" => $e->getLine(),
			"trace" => $e->getTrace()
		];
	}

	/**
	 * @param $request
	 * @param Throwable $e
	 * @return JsonResponse|RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
	 * @throws Throwable
	 */
	public function render($request, Throwable $e): \Illuminate\Http\Response|JsonResponse|\Symfony\Component\HttpFoundation\Response|RedirectResponse
	{
		/**
		 * renderable 에서 ModelNotFoundException 를 캐치 못해서 render 함수에 추가.
		 * laravel 이전 버전에서 가지고 옴.
		 */
		if ($e instanceof ModelNotFoundException) {
			return Response::error($e->getMessage() ?: __('exception.model-not-found'), $this->generateError($e), 404);
		}

		return parent::render($request, $e);
	}

	public function report(Throwable $e)
	{

	}
}
