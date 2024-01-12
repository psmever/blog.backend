<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Services\ManageService;
use Illuminate\Http\JsonResponse;
use Response;

class ManageController extends Controller
{
	protected ManageService $manageService;

	function __construct(ManageService $manageService)
	{
		$this->manageService = $manageService;
	}

	public function PostCreate(): JsonResponse
	{
		return Response::SuccessMacro($this->manageService->PostCreate());
	}

	public function PostInfo(string $uid): JsonResponse
	{
		return Response::SuccessMacro([
			'uid' => [
				$uid . "1",
				$uid . "2",
				$uid . "3",
			]
		]);

	}
}
