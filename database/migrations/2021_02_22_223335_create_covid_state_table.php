<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidStateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_state', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('gubun_id')->nullable(false)->comment('gubun id');
            $table->unsignedBigInteger('seq')->nullable(false)->comment('발행현황 고유값');

            $table->string('createdt')->nullable(false)->comment('등록일시분초.');
            $table->string('deathcnt')->nullable(false)->comment('사망자수.');
            $table->string('incdec')->nullable(false)->comment('전일대비 증감 수.');
            $table->string('isolclearcnt')->nullable(false)->comment('격리 해제 수.');
            $table->string('qurrate')->nullable(false)->comment('10만명당 발생률.');
            $table->string('stdday')->nullable(false)->comment('기준일시.');
            $table->string('updatedt')->nullable(false)->comment('수정일시분초.');
            $table->string('defcnt')->nullable(false)->comment('확진자 수.');
            $table->string('isolingcnt')->nullable(false)->comment('격리중 환자수.');
            $table->string('overflowcnt')->nullable(false)->comment('해외유입 수.');
            $table->string('localocccnt')->nullable(false)->comment('지역발생 수.');

            $table->timestamps();

            $table->foreign('gubun_id')->references('id')->on('covid_master')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('covid_state');
    }
}
