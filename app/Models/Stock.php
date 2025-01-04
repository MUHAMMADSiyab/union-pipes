<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'date',
        'quantity', // weight 
        'per_unit_weight',
        'length', // meter/foot,
        'description',
        'production_id'
    ];

    public function stock_item()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}
