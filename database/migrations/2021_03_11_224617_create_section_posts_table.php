<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false)->comment('사용자 id');
            $table->string('post_uuid')->nullable(false)->unique();
            $table->string('gubun', 6)->default('S07010')->comment('섹션 구분');
            $table->string('title')->nullable(false);
            $table->enum('markdown', ['Y', 'N'])->default('Y')->comment('마크다운 유무.');
            $table->longText('contents_html')->nullable(false);
            $table->longText('contents_text')->nullable(false);
            $table->enum('publish', ['Y', 'N'])->default('N')->comment('게시 유무.');
            $table->enum('active', ['Y', 'N'])->default('Y')->comment('글 공개 여부.');
            $table->unsignedBigInteger('view_count')->default(0)->comment('뷰 카운트.');

            $table->timestamps();

            $table->index(['user_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('gubun')->references('code_id')->on('codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_posts');
    }
}
