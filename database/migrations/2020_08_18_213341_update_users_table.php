<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_uuid', 50)->after('id')->unique()->default('')->comment('사용자 uuid');
	        $table->string('user_type', 6)->after('user_uuid')->default('S01010')->comment('사용자 타입');
	        $table->string('user_level', 6)->after('user_type')->default('S02000')->comment('사용자 레벨');
            $table->enum('active', ['Y', 'N'])->after('remember_token')->default('Y')->comment('사용자 상태');

	        $table->foreign('user_type')->references('code_id')->on('codes')->onDelete('cascade');
            $table->foreign('user_level')->references('code_id')->on('codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
		    if(DB::getDriverName() !== 'sqlite') $table->dropForeign('users_user_level_foreign');
		    if(DB::getDriverName() !== 'sqlite') $table->dropForeign('users_user_type_foreign');
		    if(DB::getDriverName() !== 'sqlite') $table->dropColumn(['user_uuid', 'user_type', 'user_level', 'active']);
        });
    }
}
