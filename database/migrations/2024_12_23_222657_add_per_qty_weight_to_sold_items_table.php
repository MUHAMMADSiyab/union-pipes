<?php

use App\Models\SoldItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerQtyWeightToSoldItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sold_items', function (Blueprint $table) {
            $table->float('per_quantity_weight')->default(0)->after('quantity');
        });

        SoldItem::all()->each(function ($soldItem) {
            if ($soldItem->quantity != 0) {
                $soldItem->per_quantity_weight = $soldItem->weight / $soldItem->quantity;
            } else {
                $soldItem->per_quantity_weight = 0;
            }
            $soldItem->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sold_items', function (Blueprint $table) {
            $table->dropColumn('per_quantity_weight');
        });
    }
}
