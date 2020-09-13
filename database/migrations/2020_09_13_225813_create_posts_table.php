<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false)->comment('사용자 id');
            $table->string('uuid')->nullable(false)->unique();
            $table->string('title')->nullable(false);
            $table->string('title_query')->nullable(false)->unique();
            $table->longText('contents_html')->nullable(false);
            $table->longText('contents_text')->nullable(false);
            $table->enum('post_active', ['Y', 'N'])->default('N')->comment('글 상태.');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
