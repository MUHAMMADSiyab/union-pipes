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
        $type = $event->type;

        foreach ($readings as $reading) {
            $tank = Tank::find($reading['tank']['id']);

            foreach ($reading['meters'] as $meter) {

                if ($type === 'sell_edit') {
                    $final_value = $sell->final_readings()
                        ->where('meter_id', $meter['id'])
                        ->first()->value;


                    if ($meter['existing_value'] > 0) {
                        $old_reading_value = $final_value - $meter['existing_value'];
                        $tank->increment('current_fuel_quantity', $old_reading_value);
                    }

                    $new_reading_value = $final_value - $meter['value'];
                    $tank->decrement('current_fuel_quantity', $new_reading_value);
                } elseif ($type === 'final_reading_update') {
                    $initial_value = $sell->initial_readings()
                        ->where('meter_id', $meter['id'])
                        ->first()->value;

                    if ($meter['existing_value'] > 0) {
                        $old_reading_value = $meter['existing_value'] - $initial_value;
                        $tank->increment('current_fuel_quantity', $old_reading_value);
                    }

                    $new_reading_value = $meter['value'] - $initial_value;
                    $tank->decrement('current_fuel_quantity', $new_reading_value);
                }
            }
        }
    }
}
