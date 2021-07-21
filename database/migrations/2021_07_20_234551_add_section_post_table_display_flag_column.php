<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionPostTableDisplayFlagColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_posts', function (Blueprint $table) {
            $table->enum('display_flag', ['Y', 'N'])->after('active')->default('N')->comment('히스토리 디스플레이 유무.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_posts', function (Blueprint $table) {
            $table->dropColumn(['display_flag']);
        });
    }
}
