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
		Schema::create('codes', function (Blueprint $table) {
			$table->id();
			$table->char('group', 6)->comment('그룹');
			$table->char('code', 6)->nullable()->unique()->comment('코드');
			$table->char('group_name', 100)->nullable()->comment('그룹명');
			$table->char('code_name', 100)->nullable()->comment('코드 네임');
			$table->enum('active', ['Y', 'N'])->default('Y')->comment('사용 상태(사용중, 비사용)');
			$table->timestamp('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('codes');
	}
};
