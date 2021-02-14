<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVilageFcstinfoMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vilage_fcstinfo_master', function (Blueprint $table) {
            $table->id();
            $table->string('version', 8)->nullable(false);
            $table->enum('active', ['Y', 'N'])->default('Y')->comment('사용 유무.');
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
        Schema::dropIfExists('vilage_fcstinfo_master');
    }
}
