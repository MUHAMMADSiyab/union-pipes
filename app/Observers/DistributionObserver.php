<?php

namespace App\Observers;

use App\Models\Distribution;
use App\Models\Tank;

class DistributionObserver
{
    /**
     * Handle the Distribution "created" event.
     *
     * @param  \App\Models\Distribution  $distribution
     * @return void
     */
    public function created(Distribution $distribution)
    {
        Tank::find($distribution->tank_id)
            ->increment('current_fuel_quantity', $distribution->new_stock_quantity);
    }

    /**
     * Handle the Distribution "updated" event.
     *
     * @param  \App\Models\Distribution  $distribution
     * @return void
     */
    public function updated(Distribution $distribution)
    {
        Tank::find($distribution->tank_id)
            ->decrement('current_fuel_quantity', $distribution->getOriginal('new_stock_quantity'));

        Tank::find($distribution->tank_id)
            ->increment('current_fuel_quantity', $distribution->new_stock_quantity);
    }

    /**
     * Handle the Distribution "deleted" event.
     *
     * @param  \App\Models\Distribution  $distribution
     * @return void
     */
    public function deleted(Distribution $distribution)
    {
        Tank::find($distribution->tank_id)
            ->decrement('current_fuel_quantity', $distribution->new_stock_quantity);
    }
}
