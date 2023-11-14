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
		Schema::table('users', function (Blueprint $table) {
			$table->string('uuid', 50)->after('id')->unique()->default('')->comment('사용자 uuid');
			$table->string('type', 6)->after('uuid')->default('010010')->comment('사용자 타입');
			$table->string('level', 6)->after('type')->default('020999')->comment('사용자 레벨');
			$table->string('nickname', 50)->after('email')->default('')->comment('사용자 닉네임');
			$table->enum('active', ['Y', 'N'])->after('remember_token')->default('Y')->comment('사용자 상태');

			$table->foreign('type')->references('code')->on('codes')->onDelete('cascade');
			$table->foreign('level')->references('code')->on('codes')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropForeign('users_type_foreign');
			$table->dropForeign('users_level_foreign');

			$table->dropColumn(['uuid', 'type', 'level', 'nickname', 'active']);
		});
	}
};
