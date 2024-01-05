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
		Schema::create('post_tags', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('post_id')->nullable(false)->index()->comment('post id.');
			$table->string('tag', 255)->nullable()->comment('테그 내용.');
			$table->timestamps();

			$table->index(['post_id', 'tag']);

			$table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('post_tags');
	}
};
