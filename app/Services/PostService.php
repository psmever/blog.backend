<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\CommonCodeRepositoryInterface;
use App\Repositories\PostRepositoryInterface;
use App\Repositories\PostStatusHistoryRepositoryInterface;
use App\Repositories\TagRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    private const POST_STATUS_GROUP = 'post.status';

    private const POST_STATUS_DRAFT = 'draft';

    private const POST_STATUS_PUBLISHED = 'published';

    public function __construct(
        private readonly CommonCodeRepositoryInterface $commonCodes,
        private readonly PostRepositoryInterface $posts,
        private readonly PostStatusHistoryRepositoryInterface $postStatusHistories,
        private readonly TagRepositoryInterface $tags
    ) {}

    public function create(User $user, array $payload): Post
    {
        return DB::transaction(function () use ($user, $payload) {
            $title = trim((string) ($payload['title'] ?? ''));
            $body = (string) ($payload['body'] ?? '');
            $tagNames = $this->normalizeTagNames($payload['tags'] ?? []);

            $slug = $this->makeUniqueSlug($user->getKey(), $title !== '' ? $title : 'post');

            $draftStatus = $this->resolveStatusCode(self::POST_STATUS_DRAFT);

            $post = $this->posts->create([
                'uuid' => $payload['uuid'] ?? (string) Str::uuid(),
                'user_id' => $user->getKey(),
                'title' => $title,
                'slug' => $slug,
                'status' => $draftStatus,
                'published_at' => null,
                'body' => $body,
            ]);

            $tagModels = $this->tags->findOrCreateByNames($tagNames);
            if ($tagModels->isNotEmpty()) {
                $post->tags()->sync($tagModels->pluck('id')->unique()->all());
            }
            $post->load('tags');

            $this->recordStatusHistory(
                $post,
                null,
                $draftStatus,
                (int) $user->getKey(),
                'create'
            );

            return $post;
        });
    }

    public function findByUuid(User $user, string $uuid): ?Post
    {
        return $this->posts->findByUuidForUser($user->getKey(), $uuid);
    }

    /**
     * @return Collection<int, Post>
     */
    public function listByStatus(User $user, string $status, int $limit): Collection
    {
        return $this->posts->listForUserByStatus(
            $user->getKey(),
            $status,
            $limit
        );
    }

    public function saveByUuid(User $user, string $uuid, array $payload): ?Post
    {
        return DB::transaction(function () use ($user, $uuid, $payload) {
            $post = $this->posts->findByUuidForUser($user->getKey(), $uuid);
            if (! $post) {
                return null;
            }

            $attributes = [];

            if (array_key_exists('title', $payload)) {
                $title = trim((string) ($payload['title'] ?? ''));
                $attributes['title'] = $title;

                if ($post->title !== $title) {
                    $attributes['slug'] = $this->makeUniqueSlug(
                        $user->getKey(),
                        $title !== '' ? $title : 'post',
                        (int) $post->getKey()
                    );
                }
            }

            if (array_key_exists('body', $payload)) {
                $attributes['body'] = (string) ($payload['body'] ?? '');
            }

            if ($attributes !== []) {
                $post = $this->posts->update($post, $attributes);
            }

            if (array_key_exists('tags', $payload)) {
                $tagNames = $this->normalizeTagNames($payload['tags'] ?? []);
                $tagModels = $this->tags->findOrCreateByNames($tagNames);
                $post->tags()->sync($tagModels->pluck('id')->unique()->all());
            }

            $draftStatus = $this->resolveStatusCode(self::POST_STATUS_DRAFT);
            $fromStatus = (string) $post->status;
            $statusNeedsUpdate = $fromStatus !== $draftStatus || $post->published_at !== null;

            if ($statusNeedsUpdate) {
                $post = $this->posts->update($post, [
                    'status' => $draftStatus,
                    'published_at' => null,
                ]);
            }

            $post->load('tags');

            $this->recordStatusHistory(
                $post,
                $fromStatus,
                $draftStatus,
                (int) $user->getKey(),
                'save'
            );

            return $post;
        });
    }

    public function publishByUuid(User $user, string $uuid): ?Post
    {
        return DB::transaction(function () use ($user, $uuid) {
            $post = $this->posts->findByUuidForUser($user->getKey(), $uuid);
            if (! $post) {
                return null;
            }

            $post->loadMissing('tags');
            $errors = [];

            if (trim((string) $post->title) === '') {
                $errors['title'] = ['제목은 필수입니다.'];
            }

            if (trim((string) $post->body) === '') {
                $errors['body'] = ['본문은 필수입니다.'];
            }

            if ($post->tags->isEmpty()) {
                $errors['tags'] = ['태그는 최소 1개 이상 필요합니다.'];
            }

            if ($errors !== []) {
                throw new ApiException(
                    '게시글을 개시하려면 필수 항목을 입력하세요.',
                    422,
                    $errors
                );
            }

            $publishedStatus = $this->resolveStatusCode(self::POST_STATUS_PUBLISHED);
            $fromStatus = (string) $post->status;

            $post = $this->posts->update($post, [
                'status' => $publishedStatus,
                'published_at' => $post->published_at ?? now(),
            ])->load('tags');

            $this->recordStatusHistory(
                $post,
                $fromStatus,
                $publishedStatus,
                (int) $user->getKey(),
                'publish'
            );

            return $post;
        });
    }

    private function makeUniqueSlug(int $userId, string $title, ?int $exceptPostId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'post';
        }

        $slug = $base;
        $suffix = 2;

        while ($this->slugExistsForUser($userId, $slug, $exceptPostId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExistsForUser(int $userId, string $slug, ?int $exceptPostId = null): bool
    {
        if ($exceptPostId === null) {
            return $this->posts->slugExistsForUser($userId, $slug);
        }

        return $this->posts->slugExistsForUserExceptPost($userId, $slug, $exceptPostId);
    }

    private function normalizeTagNames(array $tags): array
    {
        return collect($tags)
            ->filter(fn ($tag) => is_string($tag))
            ->map(fn ($tag) => trim($tag))
            ->filter(fn ($tag) => $tag !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function resolveStatusCode(string $statusCode): string
    {
        $code = $this->commonCodes->findActiveByGroupAndCode(
            self::POST_STATUS_GROUP,
            $statusCode,
            ['id']
        );

        if (! $code) {
            throw new ApiException(
                sprintf('공통코드 설정 오류입니다. [%s:%s]', self::POST_STATUS_GROUP, $statusCode),
                500
            );
        }

        return $statusCode;
    }

    private function recordStatusHistory(
        Post $post,
        ?string $fromStatus,
        string $toStatus,
        int $changedByUserId,
        string $action
    ): void {
        if ($fromStatus === $toStatus && $action !== 'save') {
            return;
        }

        $this->postStatusHistories->create([
            'post_id' => (int) $post->getKey(),
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_user_id' => $changedByUserId,
            'action' => $action,
            'changed_at' => now(),
        ]);
    }
}
