<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsInPurchaseReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_readings', function (Blueprint $table) {
            $table->decimal('rod_value', 19, 2)->default(0)->change();
            $table->decimal('available_quantity', 19, 2)->default(0)->change();
            $table->decimal('excess_quantity', 19, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_readings', function (Blueprint $table) {
            $table->decimal('rod_value', 15, 2)->default(0)->change();
            $table->decimal('available_quantity', 15, 2)->default(0)->change();
            $table->decimal('excess_quantity', 15, 2)->default(0)->change();
        });
    }
}
