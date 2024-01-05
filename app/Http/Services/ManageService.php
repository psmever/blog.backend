<?php

namespace App\Http\Services;

use App\Exceptions\ClientErrorException;
use App\Http\Repositories\PostsRepository;
use App\Http\Repositories\PostTagsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class ManageService
{
	/**
	 * @var Request
	 */
	protected Request $currentRequest;

	/**
	 * @var PostsRepository
	 */
	protected PostsRepository $postsRepository;

	/**
	 * @var PostTagsRepository
	 */
	protected PostTagsRepository $postTagsRepository;

	/**
	 * @param Request $currentRequest
	 * @param PostsRepository $postsRepository
	 * @param PostTagsRepository $postTagsRepository
	 */
	function __construct(Request $currentRequest, PostsRepository $postsRepository, PostTagsRepository $postTagsRepository)
	{
		$this->currentRequest = $currentRequest;
		$this->postsRepository = $postsRepository;
		$this->postTagsRepository = $postTagsRepository;
	}

	/**
	 * 글등록
	 * @return array
	 * @throws ClientErrorException
	 */
	public function PostCreate(): array
	{
		$request = $this->currentRequest;
		$userId = $this->currentRequest->user()->id;
		$postUid = Str::random(25);

		// 벨리데이션.
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'tags' => 'required|array|min:1',
			'tags.*' => 'required',
			'contents' => 'required|string|min:1',
		],
			[
				'title.required' => __('validator.title-required'),
				'tags.required' => __('validator.tags-required'),
				'tags.*' => __('validator.tags-*'),
				'contents.required' => __('validator.title-required'),
			]);

		if ($validator->fails()) {
			throw new ClientErrorException($validator->errors()->first());
		}

		$post = $this->postsRepository->create([
			'user_id' => $userId,
			'uuid' => $postUid,
			'title' => $request->input('title'),
			'contents' => $request->input('contents'),
			'contents_html' => $request->input('contents'),
			'publish' => 'N',
		]);

		foreach ($request->input('tags') as $element) :
			$this->postTagsRepository->create([
				'post_id' => $post->id,
				'tag' => $element,
			]);
		endforeach;

		// TODO: 썸네일 처리.
		$thumbnail = self::getThumbNailInContents($request->input('contents'));
		echo $thumbnail;

		return $post->toArray();
	}

	/**
	 * 본문에서 썸네이일 가지고 오기
	 * @param string $markdownText
	 * @return string
	 */
	private static function getThumbNailInContents(string $markdownText): string
	{
		preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $markdownText, $matches);
		$imageMatches = isset($matches[1]) && $matches[1] ? $matches[1] : [];

		if (isset($imageMatches[0]) && $imageMatches[0]) {
			return basename($imageMatches[0]);
		}

		return '';
	}
}
