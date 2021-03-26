<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPostsTableCategotyThumbColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category_thumb', 6)->after('slug_title')->default('S05000')->comment('포스트 thumb 이미지.');

            $table->foreign('category_thumb')->references('code_id')->on('codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
		    if(DB::getDriverName() !== 'sqlite') $table->dropForeign('posts_category_thumb_foreign');
		    $table->dropColumn(['category_thumb']);
        });
    }
}
