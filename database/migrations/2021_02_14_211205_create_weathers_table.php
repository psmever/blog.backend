<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
    https://www.data.go.kr/iim/api/selectAPIAcountView.do
    초단기예보 T1H 기온 ℃ 10
    RN1 1시간 강수량 범주 (1 mm) 8
    SKY 하늘상태 코드값 4
    UUU 동서바람성분 m/s 12
    VVV 남북바람성분 m/s 12
    REH 습도 % 8
    PTY 강수형태 코드값 4
    LGT 낙뢰 코드값 4
    VEC 풍향 deg 10
    WSD 풍속 m/s 10
*/

class CreateWeathersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weathers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_code_id')->nullable(false)->comment('행정구역코드 ID.');
            $table->string('fcstDate', 8)->nullable(false)->comment('예측일자.');
            $table->string('fcstTime', 8)->nullable(false)->comment('예측시간.');
            $table->string('T1H')->nullable(false)->comment('기온.');
            $table->string('RN1')->nullable(false)->comment('1시간 강수량.');
            $table->string('SKY')->nullable(false)->comment('하늘상태(맑음(1), 구름많음(3), 흐림(4))');
            $table->string('UUU')->nullable(false)->comment('동서바람성분.');
            $table->string('VVV')->nullable(false)->comment('남북바람성분.');
            $table->string('REH')->nullable(false)->comment('습도.');
            $table->string('PTY')->nullable(false)->comment('강수형태(없음(0), 비(1), 비/눈(2), 눈(3), 소나기(4), 빗방울(5), 빗방울/눈날림(6), 눈날림(7))');
            $table->string('LGT')->nullable(false)->comment('낙뢰.');
            $table->string('VEC')->nullable(false)->comment('풍향.');
            $table->string('WSD')->nullable(false)->comment('풍속.');

            $table->timestamps();

            $table->foreign('area_code_id')->references('id')->on('vilage_fcstinfo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weathers');
    }
}
