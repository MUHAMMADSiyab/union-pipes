<?php

namespace App\Listeners;

use App\Events\SellReadingsUpdated;
use App\Models\Sell;
use App\Models\Tank;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateTanksCapacity
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\SellReadingsUpdated  $event
     * @return void
     */
    public function handle(SellReadingsUpdated $event)
    {
        $readings = $event->readings;
        $sell = $event->sell;

        // Update tanks capacity
        foreach ($readings as $reading) {
            $tank = Tank::find($reading['tank_id']);

            $initial_value = $sell->initial_readings()
                ->where('nozzle_id', $reading['nozzle_id'])
                ->first()->value;

            if ($reading['existing_value'] > 0) {
                $old_reading_value = $reading['existing_value'] - $initial_value;
                $tank->increment('current_fuel_quantity', $old_reading_value);
            }

            $new_reading_value = $reading['value'] - $initial_value;
            $tank->decrement('current_fuel_quantity', $new_reading_value);
        }
    }
}
