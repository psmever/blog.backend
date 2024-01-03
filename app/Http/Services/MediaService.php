<?php

namespace App\Http\Services;

use App\Exceptions\ClientErrorException;
use App\Exceptions\ServerErrorException;
use App\Http\Repositories\MediaFilesRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemException;
use Storage;
use Str;

class MediaService
{
	/**
	 * @var Request
	 */
	protected Request $currentRequest;

	/**
	 * @var MediaFilesRepositories
	 */
	protected MediaFilesRepositories $mediaFilesRepositories;

	/**
	 * @param Request $currentRequest
	 * @param MediaFilesRepositories $mediaFilesRepositories
	 */
	function __construct(Request $currentRequest, MediaFilesRepositories $mediaFilesRepositories)
	{
		$this->currentRequest = $currentRequest;
		$this->mediaFilesRepositories = $mediaFilesRepositories;
	}

	/**
	 * 이미지 업로드 처리
	 * @return string[]
	 * @throws ClientErrorException|ServerErrorException|FilesystemException
	 */
	public function CreateAttempt(): array
	{
		$request = $this->currentRequest;

		if (!$request->hasFile('media')) {
			throw new ClientErrorException(__("validator.media-hasfile"));
		}

		if (!$request->file('media')->isValid()) {
			throw new ClientErrorException(__("validator.media-hasfile"));
		}

		$validator = Validator::make($request->all(), [
			'media' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
		],
			[
				'media.required' => __("validator.media-required"),
				'media.image' => __("validator.media-image"),
				'media.mimes' => __("validator.media-mimes"),
				'media.max' => __("validator.media-max"),
			]);

		if ($validator->fails()) {
			throw new ClientErrorException($validator->errors()->first());
		}

		$targetFileName = Str::uuid();

		[$width, $height] = getimagesize($request->file('media')->path());

		$targetDirectory = date("/Y/m/d");
		$imageExtension = $request->file('media')->extension();
		$imageOriginalName = $request->file('media')->getClientOriginalName();
		$imageWidth = $width;
		$imageHeight = $height;
		$imageSize = $request->file('media')->getSize();
		$imageMimeType = $request->file('media')->getMimeType();

		$uploadFullFileName = $targetFileName . '.' . $imageExtension;
		$uploadThumbFullFileName = $targetFileName . '_thumb.' . $imageExtension;

		if (!Storage::disk('media-server')->putFileAs($targetDirectory, $request->file('media'), $uploadFullFileName)) {
			throw new ServerErrorException(__('exception.server-error'));
		}

		if (!Storage::disk('system')->has('temp-image')) {
			Storage::disk('system')->makeDirectory('temp-image');
		}

		$manager = new ImageManager(new Driver());
		$image = $manager->read($request->file('media')->path());
		$image->resize(320, 240);
		$encoded = $image->toJpg();

		$storagePath = storage_path('system/temp-image/' . $uploadThumbFullFileName);

		$encoded->save($storagePath);

		if (!Storage::disk('media-server')->putFileAs($targetDirectory, $storagePath, $uploadThumbFullFileName)) {
			throw new ServerErrorException(__('exception.server-error'));
		}

		File::delete($storagePath);

		$this->mediaFilesRepositories->create([
			'dest_path' => $targetDirectory,
			'file_name' => $uploadFullFileName,
			'thumb_name' => $uploadThumbFullFileName,
			'original_name' => $imageOriginalName,
			'height' => $imageHeight,
			'width' => $imageWidth,
			'file_type' => $imageMimeType,
			'file_size' => $imageSize,
			'file_extension' => $imageExtension,
			'public_path' => public_path(),
		]);


		if (env('APP_ENV') === 'production') {
			return [
				'url' => [
					'image' => env('MEDIA_HOST') . '/' . 'storage' . '/' . $targetDirectory . '/' . $uploadFullFileName,
					'thumb' => env('MEDIA_HOST') . '/' . 'storage' . '/' . $targetDirectory . '/' . $uploadThumbFullFileName,
				],
				'size' => [
					'height' => $imageHeight,
					'width' => $imageWidth,
					'size' => $imageSize
				],
			];
		} else {
			return [
				'url' => [
					'image' => env('MEDIA_HOST') . ':' . env('MEDIA_PORT') . '/' . 'storage' . '/' . $targetDirectory . '/' . $uploadFullFileName,
					'thumb' => env('MEDIA_HOST') . ':' . env('MEDIA_PORT') . '/' . 'storage' . '/' . $targetDirectory . '/' . $uploadThumbFullFileName,
				],
				'size' => [
					'height' => $imageHeight,
					'width' => $imageWidth,
				],
				'file_size' => $imageSize
			];
		}
	}
}
