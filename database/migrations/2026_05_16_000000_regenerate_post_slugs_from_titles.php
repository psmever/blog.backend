<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const POST_SLUG_MAX_LENGTH = 191;

    public function up(): void
    {
        if (! Schema::hasTable('posts')) {
            return;
        }

        $posts = DB::table('posts')
            ->select(['id', 'title'])
            ->orderBy('id')
            ->get();

        foreach ($posts as $post) {
            DB::table('posts')
                ->where('id', $post->id)
                ->update(['slug' => '__refreshing_slug_'.$post->id]);
        }

        /** @var array<string, bool> $used */
        $used = [];

        foreach ($posts as $post) {
            $base = $this->makeSlugBase((string) $post->title);
            if ($base === '') {
                $base = 'post';
            }

            $base = $this->limitSlug($base);
            $slug = $base;
            $suffix = 2;

            while (isset($used[$slug])) {
                $suffixText = '-'.$suffix;
                $slug = $this->limitSlug($base, mb_strlen($suffixText, 'UTF-8')).$suffixText;
                $suffix++;
            }

            DB::table('posts')
                ->where('id', $post->id)
                ->update(['slug' => $slug]);

            $used[$slug] = true;
        }
    }

    public function down(): void
    {
        // Data migration only. Previous slug values cannot be safely restored.
    }

    private function makeSlugBase(string $title): string
    {
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower($title, 'UTF-8'));
        $slug = trim((string) $slug, '-');

        return $slug;
    }

    private function limitSlug(string $slug, int $reservedLength = 0): string
    {
        $maxLength = self::POST_SLUG_MAX_LENGTH - $reservedLength;

        return mb_substr($slug, 0, $maxLength, 'UTF-8');
    }
};
