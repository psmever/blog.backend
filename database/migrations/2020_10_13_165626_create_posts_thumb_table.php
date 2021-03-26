<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsThumbTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts_thumbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->nullable(false)->index()->comment('post id.');
            $table->unsignedBigInteger('media_file_id')->nullable()->comment('media file table id.');
            $table->timestamps();

            $table->index(['post_id', 'media_file_id']);

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('media_file_id')->references('id')->on('media_files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts_thumbs');
    }
}
