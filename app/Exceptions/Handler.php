<?php

namespace App\Exceptions;

use http\Exception\BadMethodCallException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void
	{
		$this->reportable(function (Throwable $e) {

		});

		$this->renderable(function (ClientErrorException $e, $request) {
			$statusCode = 404;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.client-error');

			$this->serverExceptionLog('ClientErrorException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (NotFoundHttpException $e, $request) {
			$statusCode = 404;
			$errorInfo = $this->generateError($e);
			$message = __('exception.not-found-http');

			$this->serverExceptionLog('NotFoundHttpException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (MethodNotAllowedHttpException $e, $request) {
			$statusCode = 405;
			$errorInfo = $this->generateError($e);
			$message = __('exception.method-not-allowed-http');

			$this->serverExceptionLog('MethodNotAllowedHttpException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (ServerErrorException $e, $request) {
			$statusCode = 500;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.server-error');

			$this->serverExceptionLog('ServerErrorException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (ErrorException $e, $request) {
			$statusCode = 400;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.error');

			$this->serverExceptionLog('ErrorException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (PDOException $e, $request) {
			$statusCode = 500;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.server-pdo-error');

			$this->serverExceptionLog('PDOException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (ForbiddenErrorException $e, $request) {
			$statusCode = 403;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.forbidden-error');

			$this->serverExceptionLog('ForbiddenErrorException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (AuthenticationException $e, $request) {
			$statusCode = 401;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.authentication');

			$this->serverExceptionLog('AuthenticationException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (ThrottleRequestsException $e, $request) {
			$statusCode = 429;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.throttle-exception');

			$this->serverExceptionLog('ThrottleRequestsException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
		});

		$this->renderable(function (BadMethodCallException $e, $request) {
			$statusCode = 404;
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.bad-method-call');

			$this->serverExceptionLog('BadMethodCallException', $message, $errorInfo);

			if ($request->wantsJson()) {
				return Response::ErrorMacro($message, $errorInfo, $statusCode);
			} else {
				return response()->view('pages.error', [
					'message' => $message,
					'error' => $errorInfo
				], $statusCode);
			}
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
			"trace" => $e->getTrace(),
			"traceString" => $e->getTraceAsString(),
		];
	}

	/**
	 * @param string $channel
	 * @param string $message
	 * @param array $exceptions
	 * @return void
	 */
	public function serverExceptionLog(string $channel, string $message, array $exceptions): void
	{
		if (App::environment(['production'])) {

			$requestindex = request()->locals['requestIndex'];

			Log::channel('exception-log')->error(<<<EOF

REQUEST_INDEX: {$requestindex}
CHANNEL: {$channel}
MESSAGE: {$message}
EXCEPTION_MESSAGE: {$exceptions['message']}
EXCEPTION_CODE: {$exceptions['code']}
EXCEPTION_FILE: {$exceptions['file']}
EXCEPTION_LINE: {$exceptions['line']}
EXCEPTION_TRACE: 
{$exceptions['traceString']}

EOF
			);
		}
	}

	public function render($request, Throwable $e)
	{
		/**
		 * renderable 에서 ModelNotFoundException 를 캐치 못해서 render 함수에 추가.
		 * laravel 이전 버전에서 가지고 옴.
		 */
		if ($e instanceof ModelNotFoundException) {

			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.model-not-foun');

			$this->serverExceptionLog('ModelNotFoundException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 404);
		}

		return parent::render($request, $e);
	}
}
