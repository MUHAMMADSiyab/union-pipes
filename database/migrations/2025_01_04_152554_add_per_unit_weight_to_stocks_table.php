<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerUnitWeightToStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->after('length', function ($table) {
                $table->float('per_unit_weight')->nullable();
            });
        });

        $stocks = \App\Models\Stock::all();
        foreach ($stocks as $stock) {
            // avoid division by zero
            if ($stock->quantity > 0 && $stock->length > 0) {
                $stock->per_unit_weight = $stock->quantity / $stock->length;
                $stock->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('per_unit_weight');
        });
    }
}
