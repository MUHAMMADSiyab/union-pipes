<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_id',
        'meter_id',
        'value',
        'final_reading',
    ];

    public function sell(): BelongsTo
    {
        return $this->belongsTo(Sell::class);
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}