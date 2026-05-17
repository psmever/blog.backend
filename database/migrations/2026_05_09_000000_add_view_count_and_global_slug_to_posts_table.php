<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'view_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('view_count')->default(0)->after('cover_image_id');
            });
        }

        $this->deduplicateSlugs();

        if (! Schema::hasIndex('posts', 'posts_user_id_index')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('user_id', 'posts_user_id_index');
            });
        }

        if (Schema::hasIndex('posts', 'posts_user_id_slug_unique', 'unique')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropUnique('posts_user_id_slug_unique');
            });
        }

        if (! Schema::hasIndex('posts', 'posts_slug_unique', 'unique')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unique('slug', 'posts_slug_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('posts', 'posts_slug_unique', 'unique')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropUnique('posts_slug_unique');
            });
        }

        if (! Schema::hasIndex('posts', 'posts_user_id_slug_unique', 'unique')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unique(['user_id', 'slug'], 'posts_user_id_slug_unique');
            });
        }

        if (Schema::hasColumn('posts', 'view_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('view_count');
            });
        }
    }

    private function deduplicateSlugs(): void
    {
        $posts = DB::table('posts')
            ->select(['id', 'slug'])
            ->orderBy('id')
            ->get();

        /** @var array<string, bool> $used */
        $used = [];

        foreach ($posts as $post) {
            $slug = is_string($post->slug) && $post->slug !== '' ? $post->slug : 'post';
            $candidate = $slug;
            $suffix = 2;

            while (isset($used[$candidate])) {
                $candidate = $slug.'-'.$suffix;
                $suffix++;
            }

            if ($candidate !== $post->slug) {
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['slug' => $candidate]);
            }

            $used[$candidate] = true;
        }
    }
};
