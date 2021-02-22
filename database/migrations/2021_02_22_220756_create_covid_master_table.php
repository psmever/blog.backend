<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_master', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false)->comment('타이틀.');
            $table->string('gubun')->nullable(false)->comment('구분 값(한글명).');
            $table->string('gubun_en')->nullable(false)->comment('구분 값(영문명).');
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
        Schema::dropIfExists('covid_master');
    }
}
