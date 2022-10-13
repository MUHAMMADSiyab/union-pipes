<?php

namespace App\Models;

use App\Traits\SellAttributesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sell extends Model
{
    use HasFactory, SellAttributesTrait;

    protected $fillable = [
        'sell_date',
        'petrol_price',
        'diesel_price'
    ];

    protected $with = ['initial_readings', 'final_readings'];

    protected $appends = [
        'petrol_initial_reading',
        'diesel_initial_reading',
        'petrol_final_reading',
        'diesel_final_reading',
        'petrol_sold_quantity',
        'diesel_sold_quantity',
        'petrol_sold_amount',
        'diesel_sold_amount',
        'total_sell_amount',
    ];

    public function initial_readings(): HasMany
    {
        return $this->hasMany(SellReading::class)->where('final_reading', false);
    }

    public function final_readings(): HasMany
    {
        return $this->hasMany(SellReading::class)->where('final_reading', true);
    }
}
