<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockSheetEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'product',
        'quantity',
        'weight',
        'rate',
        'total_weight',
        'total_amount',
    ];

    public function stock_sheet(): BelongsTo
    {
        return $this->belongsTo(StockSheet::class);
    }
}
