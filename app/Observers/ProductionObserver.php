<?php

namespace App\Observers;

use App\Models\Production;
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
        StockItem::find($production->stock_item_id)
            ->increment('available_quantity', $production->quantity);
    }

    /**
     * Handle the Production "updated" event.
     *
     * @param  \App\Models\Production  $production
     * @return void
     */
    public function updated(Production $production)
    {
        $stock_item = StockItem::find($production->stock_item_id);
        $stock_item->decrement('available_quantity', $production->getOriginal('quantity'));
        $stock_item->increment('available_quantity', $production->quantity);
    }

    /**
     * Handle the Production "deleted" event.
     *
     * @param  \App\Models\Production  $production
     * @return void
     */
    public function deleted(Production $production)
    {
        $stock_item = StockItem::find($production->stock_item_id);
        $stock_item->decrement('available_quantity', $production->quantity);
    }
}
