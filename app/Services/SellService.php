<?php

namespace App\Services;

use App\Models\SellReading;

class SellService
{
    public function saveReadings($sell, $reading, $final)
    {
        foreach ($reading['meters'] as $meter) {
            SellReading::create([
                'sell_id' => $sell->id,
                'meter_id' => $meter['id'],
                'value' => !$final ? $meter['value'] : 0,
                'final_reading' => $final,
            ]);
        }
    }
}
