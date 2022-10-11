<?php

namespace App\Services;

use App\Models\SellReading;

class SellService
{
    public function saveReadings($sell, $reading, $final)
    {
        SellReading::create([
            'sell_id' => $sell->id,
            'nozzle_id' => $reading['nozzle_id'],
            'value' => !$final ? $reading['value'] : 0,
            'final_reading' => $final,
        ]);
    }
}
