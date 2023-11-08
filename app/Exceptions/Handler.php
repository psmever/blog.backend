<?php

namespace App\Exceptions;

use http\Exception\BadMethodCallException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

		$this->renderable(function (ClientErrorException $e) {

			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.client-error');

			$this->serverExceptionLog('ClientErrorException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 400);
		});

		$this->renderable(function (NotFoundHttpException $e) {
			$errorInfo = $this->generateError($e);
			$message = __('exception.not-found-http');

			$this->serverExceptionLog('NotFoundHttpException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 404);
		});

		$this->renderable(function (MethodNotAllowedHttpException $e) {
			$errorInfo = $this->generateError($e);
			$message = __('exception.method-not-allowed-http');

			$this->serverExceptionLog('MethodNotAllowedHttpException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 405);
		});

		$this->renderable(function (ServerErrorException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.server-error');

			$this->serverExceptionLog('ServerErrorException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 500);
		});

		$this->renderable(function (ErrorException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.error');

			$this->serverExceptionLog('ErrorException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo);
		});

		$this->renderable(function (PDOException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.server-pdo-error');

			$this->serverExceptionLog('PDOException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 500);
		});

		$this->renderable(function (ForbiddenErrorException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.forbidden-error');

			$this->serverExceptionLog('ForbiddenErrorException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 403);
		});

		$this->renderable(function (AuthenticationException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.authentication');

			$this->serverExceptionLog('AuthenticationException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 401);
		});

		$this->renderable(function (ThrottleRequestsException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.throttle-exception');

			$this->serverExceptionLog('ThrottleRequestsException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 429);
		});

		$this->renderable(function (BadMethodCallException $e) {
			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.bad-method-call');

			$this->serverExceptionLog('BadMethodCallException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 404);
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

			$errorInfo = $this->generateError($e);
			$message = $e->getMessage() ?: __('exception.model-not-foun');

			$this->serverExceptionLog('ModelNotFoundException', $message, $errorInfo);

			return Response::ErrorMacro($message, $errorInfo, 404);
		}

		return parent::render($request, $e);
	}
}
