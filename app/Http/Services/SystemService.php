<?php

namespace App\Http\Services;

use App\Http\Repositories\CodesRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemService
{
	/**
	 * @var Request
	 */
	protected Request $request;

	/**
	 * @var CodesRepositories
	 */
	protected CodesRepositories $codesRepositories;

	/**
	 *
	 */
	function __construct(Request $request, CodesRepositories $codesRepositories)
	{
		$this->request = $request;
		$this->codesRepositories = $codesRepositories;
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

	/**
	 * 공통 데이터
	 * @return array
	 */
	function systemAppData(): array
	{
		$codeResult = $this->codesRepositories->all();
		return [
			'code' => call_user_func(function () use ($codeResult) {
				return [
					'step1' => $codeResult->map(function ($code) {
						return [
							'code' => $code->code,
							'name' => $code->code_name,
						];
					})->filter(function ($e) {
						return $e['code'];
					})->values(),
					'step2' => call_user_func(function () use ($codeResult) {
						$codes = array();
						foreach ($codeResult as $element) {
							if ($element->group && $element->code) {
								$codes[$element->group][] = [
									'code' => $element->code,
									'name' => $element->code_name,
								];
							}
						}
						return $codes;
					})
				];
			})

		];
	}
}
