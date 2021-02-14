<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVilageFcstinfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vilage_fcstinfo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('version_id')->nullable(false)->comment('버전 id.');
            $table->string('gubun')->nullable(false)->comment('구분.');
            $table->string('area_code')->nullable(false)->comment('행정구역코드.');
            $table->string('step1')->nullable(false)->comment('1단계.');
            $table->string('step2')->nullable(false)->comment('2단계.');
            $table->string('step3')->nullable(false)->comment('3단계.');
            $table->string('grid_x')->nullable(false)->comment('격자 X.');
            $table->string('grid_y')->nullable(false)->comment('격자 Y.');
            $table->string('longitude_hour')->nullable(false)->comment('경도(시).');
            $table->string('longitude_minute')->nullable(false)->comment('경도(분).');
            $table->string('longitude_second')->nullable(false)->comment('경도(초).');
            $table->string('latitude_hour')->nullable(false)->comment('위도(시).');
            $table->string('latitude_minute')->nullable(false)->comment('위도(분).');
            $table->string('latitude_second')->nullable(false)->comment('위도(초).');
            $table->string('longitude')->nullable(false)->comment('경도(초/100).');
            $table->string('latitude')->nullable(false)->comment('위도(초/100).');
            $table->string('update_time', 8)->nullable(false)->comment('위치업데이트.');
            $table->enum('active', ['Y', 'N'])->default('Y')->comment('사용 유무.');
            $table->timestamps();

            $table->foreign('version_id')->references('id')->on('vilage_fcstinfo_master')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vilage_fcstinfo');
    }
}
