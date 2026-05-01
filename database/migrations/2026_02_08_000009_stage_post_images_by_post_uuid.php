<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('post_images', 'post_uuid')) {
            return;
        }

        Schema::table('post_images', function (Blueprint $table) {
            $table->uuid('post_uuid')->nullable()->after('user_id');
        });

        DB::table('post_images')
            ->whereNotNull('post_id')
            ->orderBy('id')
            ->get()
            ->each(function ($image) {
                $postUuid = DB::table('posts')
                    ->where('id', $image->post_id)
                    ->value('uuid');

                if ($postUuid) {
                    DB::table('post_images')
                        ->where('id', $image->id)
                        ->update(['post_uuid' => $postUuid]);
                }
            });

        Schema::table('post_images', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->foreignId('post_id')->nullable()->change();
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->index(['user_id', 'post_uuid', 'purpose']);
        });
    }

    public function down(): void
    {
        //
    }
};
