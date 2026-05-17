<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('posts', 'cover_image_id')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('cover_image_id')
                ->nullable()
                ->after('published_at')
                ->constrained('post_images')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cover_image_id');
        });
    }
};
