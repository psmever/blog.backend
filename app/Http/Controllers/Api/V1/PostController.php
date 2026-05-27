<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostImageService;
use App\Services\PostService;
use App\Support\PostImageResponseFormatter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends ApiBaseController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly PostImageService $postImageService,
        private readonly PostImageResponseFormatter $postImageFormatter
    ) {}

    public function issueUuid(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        return $this->responseSuccess([
            'uuid' => $this->postService->issueUuid(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $payload = $request->validate([
            'uuid' => ['sometimes', 'uuid', Rule::unique('posts', 'uuid')],
            'title' => ['nullable', 'string', 'max:200'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'min:1', 'max:30', 'regex:/^[\pL\pN][\pL\pN\s\-\.\+#_]*$/u'],
            'body' => ['nullable', 'string'],
        ]);

        $payload['uuid'] = $payload['uuid'] ?? (string) Str::uuid();

        $post = $this->postService->create($user, $payload);

        return $this->responseSuccess(
            $this->formatWritePostResponse($post),
            '정상 처리되었습니다',
            Response::HTTP_CREATED
        );
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $payload = $request->validate([
            'status' => ['sometimes', 'string', 'in:'.Post::STATUS_DRAFT.','.Post::STATUS_PUBLISHED],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $status = (string) ($payload['status'] ?? Post::STATUS_PUBLISHED);
        $limit = (int) ($payload['limit'] ?? 20);
        $posts = $this->postService->listByStatus($user, $status, $limit);

        return $this->responseSuccess(
            $posts
                ->map(fn (Post $post) => [
                    'uuid' => $post->uuid,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'status' => $post->status,
                    'published_at' => $this->formatDateTimeForResponse($post->published_at),
                    'updated_at' => $this->formatDateTimeForResponse($post->updated_at),
                ])
                ->values()
                ->all()
        );
    }

    public function show(Request $request, string $uuid)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $post = $this->postService->findByUuid($user, $uuid);
        if (! $post) {
            return $this->responseNotFound('게시글을 찾을 수 없습니다.');
        }

        /** @var Collection<int, Tag> $tags */
        $tags = $post->tags;

        return $this->responseSuccess([
            'uuid' => $post->uuid,
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
            'published_at' => $this->formatDateTimeForResponse($post->published_at),
            'cover_image' => $this->postImageFormatter->format($post->coverImage),
            'tags' => $tags
                ->map(fn (Tag $tag) => ['key' => $tag->key, 'label' => $tag->label])
                ->values()
                ->all(),
            'body' => $post->body,
            'created_at' => $this->formatDateTimeForResponse($post->created_at),
            'updated_at' => $this->formatDateTimeForResponse($post->updated_at),
        ]);
    }

    public function save(Request $request, string $uuid)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $payload = $request->validate([
            'title' => ['sometimes', 'nullable', 'string', 'max:200'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'min:1', 'max:30', 'regex:/^[\pL\pN][\pL\pN\s\-\.\+#_]*$/u'],
            'body' => ['sometimes', 'nullable', 'string'],
        ]);

        $post = $this->postService->saveByUuid($user, $uuid, $payload);
        if (! $post) {
            return $this->responseNotFound('게시글을 찾을 수 없습니다.');
        }

        return $this->responseSuccess(
            $this->formatWritePostResponse($post),
            '임시 저장되었습니다.'
        );
    }

    public function uploadImage(Request $request, string $uuid)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        if (! $this->postImageService->isUploadAvailable()) {
            return $this->responseError(
                '이미지 업로드를 사용할 수 없습니다. APP_IMAGE_URL 설정이 필요합니다.',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $payload = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:'.config('posts.image_upload_max_kb')],
        ]);

        $image = $this->postImageService->uploadForPost($user, $uuid, $payload['image']);
        if (! $image) {
            return $this->responseNotFound('게시글을 찾을 수 없습니다.');
        }

        return $this->responseSuccess(
            $this->postImageFormatter->format($image),
            '이미지가 업로드되었습니다.',
            Response::HTTP_CREATED
        );
    }

    public function publish(Request $request, string $uuid)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $post = $this->postService->publishByUuid($user, $uuid);
        if (! $post) {
            return $this->responseNotFound('게시글을 찾을 수 없습니다.');
        }

        return $this->responseSuccess(
            $this->formatWritePostResponse($post),
            '게시되었습니다.'
        );
    }

    private function formatWritePostResponse(Post $post): array
    {
        return [
            'uuid' => $post->uuid,
            'slug' => $post->slug,
            'public_url' => '/api/v1/public/posts/'.$post->slug,
        ];
    }
}
