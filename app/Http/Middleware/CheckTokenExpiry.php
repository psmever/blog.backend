<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\TransientToken;

class CheckTokenExpiry
{
    /**
     * Ensure the Sanctum access token used for the request is still valid.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->user()?->currentAccessToken();
        /** @var PersonalAccessToken|TransientToken|null $token */
        if (! $token) {
            return response()->json(['message' => '인증 정보가 유효하지 않습니다.'], 401);
        }

        if (! ($token instanceof PersonalAccessToken)) {
            return $next($request);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return response()->json(['message' => '토큰이 만료되었습니다. 다시 로그인해 주세요.'], 401);
        }

        return $next($request);
    }
}
