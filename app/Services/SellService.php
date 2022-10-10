<?php

namespace App\Services;

class SellService
{
    public function saveReadings($sell, $reading, $final)
    {
        $sell->readings()->create([
            'nozzle_id' => $reading['nozzle_id'],
            'value' => !$final ? $reading['value'] : 0,
            'final_reading' => $final,
        ]);
    }
}
