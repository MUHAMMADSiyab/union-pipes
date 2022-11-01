<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFieldsInPurchaseReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_readings', function (Blueprint $table) {
            $table->decimal('rod_value', 15, 2)->default(0)->change();
            $table->decimal('available_quantity', 15, 2)->default(0)->change();
            $table->decimal('excess_quantity', 15, 2)->default(0)->change();
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
            $table->float('rod_value')->default(0)->change();
            $table->float('available_quantity')->default(0)->change();
            $table->float('excess_quantity')->default(0)->change();
        });
    }
}
