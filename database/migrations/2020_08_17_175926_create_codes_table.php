<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->char('group_id', 6);
            $table->char('code_id', 6)->nullable()->unique();
            $table->char('group_name', 100)->nullable();
            $table->char('code_name', 100)->nullable();
            $table->enum('active', ['Y', 'N'])->default('Y')->comment('사용 상태(사용중, 비사용)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('codes');
    }
}
