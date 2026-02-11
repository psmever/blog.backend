<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends ApiBaseController
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $this->responseUnauthorized();
        }

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['string', 'min:1', 'max:30', 'regex:/^[\pL\pN][\pL\pN\s\-\.\+#_]*$/u'],
            'body' => ['required', 'string'],
        ]);

        $post = $this->postService->create($user, $payload);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags */
        $tags = $post->tags;

        return $this->responseSuccess([
            'id' => $post->getKey(),
            'title' => $post->title,
            'slug' => $post->slug,
            'tags' => $tags




                ->map(fn (Tag $tag) => ['key' => $tag->key, 'label' => $tag->label])
                ->values()
                ->all(),
            'body' => $post->body,
            'created_at' => $this->formatDateTimeForResponse($post->created_at),
            'updated_at' => $this->formatDateTimeForResponse($post->updated_at),
        ], '정상 처리되었습니다', Response::HTTP_CREATED);
    }
}
