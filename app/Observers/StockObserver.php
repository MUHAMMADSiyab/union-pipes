<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\StockItem;

class StockObserver
{
    /**
     * Handle the Stock "created" event.
     *
     * @param  \App\Models\Stock  $stock
     * @return void
     */
    public function created(Stock $stock)
    {
        StockItem::find($stock->stock_item_id)
            ->increment('available_quantity', $stock->quantity);
    }

    /**
     * Handle the Stock "updated" event.
     *
     * @param  \App\Models\Stock  $stock
     * @return void
     */
    public function updated(Stock $stock)
    {
        $stock_item = StockItem::find($stock->stock_item_id);
        $stock_item->decrement('available_quantity', $stock->getOriginal('quantity'));
        $stock_item->increment('available_quantity', $stock->quantity);
    }

    /**
     * Handle the Stock "deleted" event.
     *
     * @param  \App\Models\Stock  $stock
     * @return void
     */
    public function deleted(Stock $stock)
    {
        $stock_item = StockItem::find($stock->stock_item_id);
        $stock_item->decrement('available_quantity', $stock->quantity);
    }
}
