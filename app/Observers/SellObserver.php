<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Sell;
use App\Models\StockItem;

class SellObserver
{
    /**
     * Handle the Sell "created" event.
     *
     * @param  \App\Models\Sell  $sell
     * @return void
     */
    public function created(Sell $sell) {}

    /**
     * Handle the Sell "updated" event.
     *
     * @param  \App\Models\Sell  $sell
     * @return void
     */
    public function updated(Sell $sell) {}

    /**
     * Handle the Sell "deleted" event.
     *
     * @param  \App\Models\Sell  $sell
     * @return void
     */
    public function deleting(Sell $sell)
    {
        $stock_item = StockItem::first();

        info($sell->sold_items);

        if ($stock_item) {
            foreach ($sell->sold_items as $soldItem) {
                $stock_item->increment('available_quantity', $soldItem->weight);
            }
        }
    }

    /**
     * Handle the Sell "restored" event.
     *
     * @param  \App\Models\Sell  $sell
     * @return void
     */
    public function restored(Sell $sell)
    {
        //
    }

    /**
     * Handle the Sell "force deleted" event.
     *
     * @param  \App\Models\Sell  $sell
     * @return void
     */
    public function forceDeleted(Sell $sell)
    {
        //
    }
}
