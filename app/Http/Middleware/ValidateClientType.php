<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\CommonCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateClientType
{
    private const GROUP_KEY = 'client.type';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->is('api/*')) {
            return $next($request);
        }

        $clientType = trim((string) $request->header('Client-Type', ''));

        if ($clientType === '') {
            throw new ApiException(
                'Client-Type 헤더가 필요합니다.',
                400,
                ['Client-Type' => ['Client-Type 헤더가 비어 있습니다.']]
            );
        }

        $code = CommonCode::query()
            ->forGroup(self::GROUP_KEY)
            ->active()
            ->where('code', $clientType)
            ->first();

        if (! $code) {
            throw new ApiException(
                'Client-Type 헤더 값이 올바르지 않습니다.',
                400,
                ['Client-Type' => ['허용되지 않은 Client-Type 값입니다.']]
            );
        }

        $request->attributes->set('clientTypeCode', $code->code);
        $request->attributes->set('clientType', $code);

        return $next($request);
    }
}
