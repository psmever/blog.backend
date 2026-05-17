<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PublicPostService;
use App\Support\PostImageResponseFormatter;
use Illuminate\Http\Request;

class PublicPostController extends ApiBaseController
{
    public function __construct(
        private readonly PublicPostService $publicPosts,
        private readonly PostImageResponseFormatter $postImageFormatter
    ) {}

    public function index(Request $request)
    {
        $payload = $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'cursor' => ['sometimes', 'string'],
        ]);

        $limit = (int) ($payload['limit'] ?? 12);
        $cursor = isset($payload['cursor']) ? (string) $payload['cursor'] : null;
        [$posts, $nextCursor, $hasMore] = $this->publicPosts->listPublished($limit, $cursor);

        return $this->responseCursorPaginated(
            $posts
                ->map(fn (Post $post) => $this->formatListItem($post))
                ->values()
                ->all(),
            $limit,
            $nextCursor,
            $hasMore
        );
    }

    public function show(Request $request, string $slug)
    {
        $post = $this->publicPosts->findPublishedBySlug($slug, $request);
        if (! $post) {
            return $this->responseNotFound('게시글을 찾을 수 없습니다.');
        }

        return $this->responseSuccess($this->formatDetailItem($post));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatListItem(Post $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $this->publicPosts->excerptFromBody($post->body),
            'published_at' => $this->formatDateTimeForResponse($post->published_at),
            'cover_image' => $this->postImageFormatter->format($post->coverImage),
            'author' => [
                'name' => $post->user?->name ?? '',
            ],
            'primary_tag' => $this->formatPrimaryTag($post),
            'view_count' => (int) $post->view_count,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatDetailItem(Post $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $this->publicPosts->excerptFromBody($post->body),
            'published_at' => $this->formatDateTimeForResponse($post->published_at),
            'cover_image' => $this->postImageFormatter->format($post->coverImage),
            'author' => [
                'name' => $post->user?->name ?? '',
            ],
            'tags' => $post->tags
                ->sortBy('label')
                ->values()
                ->map(fn (Tag $tag) => [
                    'key' => $tag->key,
                    'label' => $tag->label,
                ])
                ->all(),
            'body' => $post->body,
            'view_count' => (int) $post->view_count,
            'created_at' => $this->formatDateTimeForResponse($post->created_at),
            'updated_at' => $this->formatDateTimeForResponse($post->updated_at),
        ];
    }

    /**
     * @return array{key: string, label: string}|null
     */
    private function formatPrimaryTag(Post $post): ?array
    {
        /** @var ?Tag $tag */
        $tag = $post->tags->sortBy('label')->first();

        if (! $tag) {
            return null;
        }

        return [
            'key' => $tag->key,
            'label' => $tag->label,
        ];
    }
}
