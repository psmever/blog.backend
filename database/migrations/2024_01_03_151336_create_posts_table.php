<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('posts', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id')->nullable(false)->comment('사용자 id');
			$table->string('uuid', 50)->nullable(false)->unique()->comment('고유 id.');
			$table->string('slug')->nullable(false)->unique()->comment('slug 타이틀.');
			$table->string('title')->nullable(false)->comment('제목.');
			$table->longText('contents')->nullable(false)->comment('내용(마크다운).');
			$table->longText('contents_html')->nullable(false)->comment('내용(html).');
			$table->enum('publish', ['Y', 'N'])->default('N')->comment('게시 유무.');
			$table->unsignedBigInteger('view')->default(0)->comment('뷰 카운트.');

			$table->timestamps();
			$table->softDeletes();

			$table->index(['slug', 'user_id']);
			$table->index(['uuid']);
			$table->index(['title']);
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('posts');
	}
};
