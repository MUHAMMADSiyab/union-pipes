<?php

namespace App\Traits;

trait SellAttributesTrait
{
    public function getTotalInitialReadingAttribute()
    {
        return $this->initial_readings()->sum('value');
    }

    public function getTotalFinalReadingAttribute()
    {
        return $this->final_readings()->sum('value');
    }

    public function getSoldQuantityAttribute()
    {
        return $this->total_final_reading - $this->total_initial_reading;
    }

    public function getInitialReadingAmountAttribute()
    {
        return $this->getReadingAmount('initial_readings');
    }

    public function getFinalReadingAmountAttribute()
    {
        return $this->getReadingAmount('final_readings');
    }

    public function getTotalAmountAttribute()
    {
        return $this->final_reading_amount - $this->initial_reading_amount;
    }

    public function getReadingAmount($reading_type)
    {
        $readings = $this->{$reading_type}()
            ->with([
                'nozzle',
                'nozzle.dispenser',
                'nozzle.dispenser.tank',
                'nozzle.dispenser.tank.product'
            ])
            ->get();

        $petrolAmount = $readings->sum(function ($reading) {
            if ($reading->nozzle->dispenser->tank->product->name === "Petrol") {
                return $this->petrol_price * $reading->value;
            }
        });

        $dieselAmount = $readings->sum(function ($reading) {
            if ($reading->nozzle->dispenser->tank->product->name === "Diesel") {
                return $this->diesel_price * $reading->value;
            }
        });

        return $petrolAmount + $dieselAmount;
    }
}
