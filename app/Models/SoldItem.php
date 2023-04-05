<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_id',
        'product_id',
        'quantity',
        'weight',
        'rate',
        'total',
        'sales_tax',
        'grand_total',
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
