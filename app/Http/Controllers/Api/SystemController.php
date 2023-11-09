<?php

namespace App\Http\Controllers\Api;

use App\Service\SystemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class SystemController extends RootController
{
	/**
	 * @var SystemService
	 */
	protected SystemService $systemService;

	/**
	 * @param SystemService $systemService
	 */
	function __construct(SystemService $systemService)
	{
		$this->systemService = $systemService;
	}

	// 스스템 상태 체크
	public function SystemStatus(): JsonResponse
	{
		return Response::SuccessNoContentMacro();
	}

	// 시스템 공지 사항
	public function SystemNotice()
	{
		$notice = $this->systemService->systemNotice();

		if (empty($notice)) {
			return Response::SuccessNoContentMacro();
		} else {
			return Response::SuccessMacro([
				'contents' => $notice
			]);
		}
	}
}
