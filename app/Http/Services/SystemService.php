<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Storage;

class SystemService
{
	/**
	 *
	 */
	function __construct()
	{
		//
	}

	/**
	 * 시스템 공지 사항 체크
	 * @return string
	 */
	function systemNotice(): string
	{
		$noticeFileName = 'system-notice.txt';
		$noticeExists = Storage::disk('system')->exists($noticeFileName);

		// 시스템 공지 사항 없을때.
		if (!$noticeExists) {
			return "";
		}

		// 시스템 공지 사항 있을때.
		$noticeContents = Storage::disk('system')->get($noticeFileName);
		if (empty($noticeContents)) {
			return "";
		}

		return $noticeContents;
	}

	function systemAppData(): array
	{

	}
}
