<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    public function __construct(
        string $message = 'Bad Request',
        public int $status = 400,
        public ?array $errors = null
    ) {
        parent::__construct($message, $status);
    }

    // Laravel은 예외에 render() 메서드가 있으면 우선 사용해줘요.
    public function render(Request $request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            $res = ['message' => $this->getMessage()];
            if ($this->errors) $res['errors'] = $this->errors;
            return response()->json($res, $this->status);
        }
        // web 요청이면 기본 처리(에러 페이지 등)로 넘김
        return null;
    }
}
