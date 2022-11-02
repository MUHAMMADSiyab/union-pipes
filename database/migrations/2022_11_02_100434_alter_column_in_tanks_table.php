<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnInTanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->decimal('current_fuel_quantity', 19, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->float('current_fuel_quantity')->default(0)->change();
        });
    }
}
