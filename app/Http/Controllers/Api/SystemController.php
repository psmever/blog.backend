<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class SystemController extends RootController
{
	public function SystemStatus(): JsonResponse
	{
		return Response::SuccessNoContentMacro();
	}
}
