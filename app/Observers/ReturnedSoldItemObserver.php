<?php

namespace App\Observers;

use App\Models\ReturnedSoldItem;
use App\Models\Sell;
use App\Models\SoldItem;
use App\Models\StockItem;

class ReturnedSoldItemObserver
{
    /**
     * Handle the ReturnedSoldItem "created" event.
     *
     * @param  \App\Models\ReturnedSoldItem  $returnedSolItem
     * @return void
     */
    public function created(ReturnedSoldItem $returnedSolItem)
    {
        $sold_item = SoldItem::query()
            ->where('sell_id', $returnedSolItem->sell_id)
            ->where('product_id', $returnedSolItem->product_id)
            ->first();

        $sold_item->decrement('weight', $returnedSolItem->weight);
        $sold_item->decrement('quantity', $returnedSolItem->quantity);
        $sold_item->decrement('total', $returnedSolItem->total);
        $sold_item->decrement('grand_total', $returnedSolItem->total);

        // Decrement sell total amount
        Sell::find($sold_item->sell_id)->decrement('total_amount', $returnedSolItem->total);

        // Add back weight and length to stock
        // $stock_item = StockItem::first();

        // if ($stock_item) {
        //     $stock_item->increment('available_quantity', $returnedSolItem->weight);
        // }

        $sell = $returnedSolItem->sell;
        if (is_null($sell->stock_item_id)) {
            $stock_item = StockItem::query()->where('product_id', $returnedSolItem->product_id)->first();
        } else {
            $stock_item = StockItem::find($sell->stock_item_id);
        }

        if ($stock_item) {
            $stock_item->increment('available_quantity', $returnedSolItem->weight);
            $stock_item->increment('available_length', $returnedSolItem->quantity);
        }
    }

    /**
     * Handle the ReturnedSoldItem "updated" event.
     *
     * @param  \App\Models\ReturnedSoldItem  $returnedSolItem
     * @return void
     */
    public function updated(ReturnedSoldItem $returnedSolItem)
    {
        //
    }

    /**
     * Handle the ReturnedSoldItem "deleted" event.
     *
     * @param  \App\Models\ReturnedSoldItem  $returnedSolItem
     * @return void
     */
    public function deleted(ReturnedSoldItem $returnedSolItem)
    {
        //
    }

    /**
     * Handle the ReturnedSoldItem "restored" event.
     *
     * @param  \App\Models\ReturnedSoldItem  $returnedSolItem
     * @return void
     */
    public function restored(ReturnedSoldItem $returnedSolItem)
    {
        //
    }

    /**
     * Handle the ReturnedSoldItem "force deleted" event.
     *
     * @param  \App\Models\ReturnedSoldItem  $returnedSolItem
     * @return void
     */
    public function forceDeleted(ReturnedSoldItem $returnedSolItem)
    {
        //
    }
}
