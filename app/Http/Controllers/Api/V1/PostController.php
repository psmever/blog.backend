<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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

        $payload['uuid'] = (string) Str::uuid();

        $post = $this->postService->create($user, $payload);

        return $this->responseSuccess([
            'uuid' => $post->uuid,
        ], '정상 처리되었습니다', Response::HTTP_CREATED);
    }
}
