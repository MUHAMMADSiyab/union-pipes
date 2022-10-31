<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_transactions', function (Blueprint $table) {
            $table->id();
            $table->float('vehicle_charges');
            $table->float('expense')->default(0);
            $table->dateTime('date');
            $table->string('driver');
            $table->foreignId('purchase_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_transactions');
    }
}
