<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'purchase_item_id',
        'quantity',
        'rate',
        'total',
        'sales_tax',
        'grand_total',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchase_item()
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}
