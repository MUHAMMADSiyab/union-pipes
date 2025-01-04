<?php

namespace App\Observers;

use App\Models\Production;
use App\Models\Stock;
use App\Models\StockItem;

class ProductionObserver
{
    /**
     * Handle the Production "created" event.
     *
     * @param  \App\Models\Production  $production
     * @return void
     */
    public function created(Production $production)
    {
        $stock_item = StockItem::query()
            ->where('product_id', $production->product_id)
            ->first();

        if ($stock_item) {
            $stock_item->stocks()->create([
                'production_id' => $production->id,
                'date' => $production->date,
                'quantity' => $production->total_weight,
                'length' => $production->quantity,
                'description' => $production->description
            ]);
        }
    }

    /**
     * Handle the Production "updated" event.
     *
     * @param  \App\Models\Production  $production
     * @return void
     */
    public function updated(Production $production)
    {
        // Delete existing one
        Stock::query()
            ->where('production_id', $production->id)
            ->first()
            ?->delete();

        $stock_item = StockItem::query()
            ->where('product_id', $production->product_id)
            ->first();

        if ($stock_item) {
            $stock_item->stocks()->create([
                'production_id' => $production->id,
                'date' => $production->date,
                'quantity' => $production->total_weight,
                'length' => $production->quantity,
                'description' => $production->description
            ]);
        }
    }
}
