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

    protected $appends = [
        'total_initial_reading',
        'total_final_reading',
        'initial_reading_amount',
        'final_reading_amount',
        'total_amount', // total sell
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
