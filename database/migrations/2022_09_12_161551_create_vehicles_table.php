<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('chassis_no')->nullable();
            $table->string('contractor_name')->nullable();
            $table->date('calibration_date');
            $table->string('receipt_no')->nullable();
            $table->date('validity');
            $table->float('capacity')->default(0);
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
        Schema::dropIfExists('vehicles');
    }
}
