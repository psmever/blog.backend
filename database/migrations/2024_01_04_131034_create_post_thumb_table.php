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
		Schema::create('post_thumb', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('post_id')->nullable(false)->index()->comment('post id.');
			$table->unsignedBigInteger('media_id')->nullable(false)->index()->comment('media file id.');
			$table->timestamps();

			$table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
			$table->foreign('media_id')->references('id')->on('media_files')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('post_thumb');
	}
};
