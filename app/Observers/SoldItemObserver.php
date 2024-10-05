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
        $sell = $soldItem->sell;
        if (is_null($sell->stock_item_id)) {
            $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
        } else {
            $stock_item = StockItem::find($sell->stock_item_id);
        }

        if ($stock_item) {
            $stock_item->decrement('available_quantity', $soldItem->weight);
            $stock_item->decrement('available_length', $soldItem->quantity);
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
        // info($soldItem->getOriginal('sell'));
        // $sell = $soldItem->sell;
        // if (is_null($sell->stock_item_id)) {
        //     $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
        // } else {
        //     $stock_item = StockItem::find($sell->stock_item_id);
        // }

        // if ($stock_item) {
        //     $stock_item->decrement('available_quantity', $soldItem->weight);
        //     $stock_item->decrement('available_length', $soldItem->quantity);
        // }
        // $stock_item = StockItem::first();

        // if ($stock_item) {
        //     $stock_item->increment('available_quantity', $soldItem->getOriginal('weight'));
        //     $stock_item->increment('available_length', $soldItem->getOriginal('quantity'));
        // }
    }
}
