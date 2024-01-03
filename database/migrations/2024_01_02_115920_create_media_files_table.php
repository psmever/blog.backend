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
		Schema::create('media_files', function (Blueprint $table) {
			$table->id();
			$table->string('dest_path')->nullable(false)->comment('저장 디렉토리.');
			$table->string('file_name')->nullable(false)->comment('파일명.');
			$table->string('thumb_name')->nullable(false)->comment('썸네일 파일명.');
			$table->string('original_name')->nullable(false)->comment('원본 파일명.');
			$table->string('height')->nullable(false);
			$table->string('width')->nullable(false);
			$table->string('file_type', 50)->nullable(false)->comment('원본 파일 타입.');
			$table->bigInteger('file_size')->nullable(false)->comment('파일 용량.');
			$table->string('file_extension', 50)->nullable(false)->comment('파일 확장자.');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('media_files');
	}
};
