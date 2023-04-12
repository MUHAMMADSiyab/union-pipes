<?php

namespace App\Observers;

use App\Models\SoldItem;
use App\Models\StockItem;

class SoldItemObserver
{
    /**
     * Handle the SoldItem "created" event.
     *
     * @param  \App\Models\SoldItem  $soldItem
     * @return void
     */
    public function created(SoldItem $soldItem)
    {
        $stock_item = StockItem::first();

        if ($stock_item) {
            $stock_item->decrement('available_quantity', $soldItem->weight);
        }
    }

    /**
     * Handle the SoldItem "updated" event.
     *
     * @param  \App\Models\SoldItem  $soldItem
     * @return void
     */
    public function updated(SoldItem $soldItem)
    {
        $stock_item = StockItem::first();

        if ($stock_item) {
            $stock_item->increment('available_quantity', $soldItem->getOriginal('weight'));
            $stock_item->decrement('available_quantity', $soldItem->weight);
        }
    }

    /**
     * Handle the SoldItem "deleted" event.
     *
     * @param  \App\Models\SoldItem  $soldItem
     * @return void
     */
    public function deleted(SoldItem $soldItem)
    {
        $stock_item = StockItem::first();

        if ($stock_item) {
            $stock_item->increment('available_quantity', $soldItem->getOriginal('weight'));
        }
    }
}
