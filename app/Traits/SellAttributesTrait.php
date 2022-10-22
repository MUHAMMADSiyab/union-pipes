<?php

namespace App\Traits;

trait SellAttributesTrait
{
    public function getPetrolInitialReadingAttribute()
    {
        return $this->getReading('initial_readings', 'Petrol');
    }

    public function getDieselInitialReadingAttribute()
    {
        return $this->getReading('initial_readings', 'Diesel');
    }

    public function getPetrolFinalReadingAttribute()
    {
        return $this->getReading('final_readings', 'Petrol');
    }

    public function getDieselFinalReadingAttribute()
    {
        return $this->getReading('final_readings', 'Diesel');
    }

    public function getPetrolSoldQuantityAttribute()
    {
        return ($this->petrol_final_reading - $this->petrol_initial_reading) < 0 ?
            0 :
            $this->petrol_final_reading - $this->petrol_initial_reading;
    }

    public function getDieselSoldQuantityAttribute()
    {
        return ($this->diesel_final_reading - $this->diesel_initial_reading) < 0 ?
            0 :
            $this->diesel_final_reading - $this->diesel_initial_reading;
    }


    public function getPetrolSoldAmountAttribute()
    {
        $sold_quantity = $this->petrol_final_reading - $this->petrol_initial_reading;
        return ($sold_quantity * $this->petrol_price) < 0 ?
            0 : ($sold_quantity * $this->petrol_price);
    }

    public function getDieselSoldAmountAttribute()
    {
        $sold_quantity = $this->diesel_final_reading - $this->diesel_initial_reading;
        return ($sold_quantity * $this->diesel_price) < 0 ?
            0 : ($sold_quantity * $this->diesel_price);
    }

    public function getTotalSellAmountAttribute()
    {
        return $this->petrol_sold_amount + $this->diesel_sold_amount;
    }


    public function getReading($type, $product)
    {
        $readings = $this->{$type}()->with([
            'meter',
            'meter.dispenser',
            'meter.dispenser.tank',
            'meter.dispenser.tank.product'
        ])
            ->get();

        return $readings->sum(function ($reading) use ($product) {
            if ($reading->meter->dispenser->tank->product->name === $product) {
                return $reading->value;
            }
        });
    }
}
