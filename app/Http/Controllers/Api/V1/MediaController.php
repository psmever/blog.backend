<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ClientErrorException;
use App\Exceptions\ServerErrorException;
use App\Http\Controllers\Controller;
use App\Http\Services\MediaService;
use Illuminate\Http\JsonResponse;
use League\Flysystem\FilesystemException;
use Response;

class MediaController extends Controller
{
	/**
	 * @var MediaService
	 */
	protected MediaService $mediaService;

	/**
	 * @param MediaService $mediaService
	 */
	function __construct(MediaService $mediaService)
	{
		$this->mediaService = $mediaService;
	}

	/**
	 * @return JsonResponse
	 * @throws ClientErrorException
	 * @throws ServerErrorException
	 * @throws FilesystemException
	 */
	public function Create(): JsonResponse
	{
		return Response::SuccessMacro($this->mediaService->CreateAttempt());
	}
}
