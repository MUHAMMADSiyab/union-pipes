<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'date',
        'quantity',
    ];

    public function stock_item()
    {
        return $this->belongsTo(StockItem::class);
    }
}
