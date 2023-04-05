<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnedSoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'sell_id',
        'product_id',
        'quantity',
        'weight',
        'rate',
        'total',
    ];

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'sell_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
