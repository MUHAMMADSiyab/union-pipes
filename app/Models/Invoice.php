<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'invoice_no',
        'buyer',
        'address',
        'ntn_no',
        'gst_no',
        'product',
        'quantity',
        'rate',
        'sales_tax_rate',
        'date',
    ];

    protected $appends = [
        'total_amount',
        'sales_tax_amount',
        'amount_including_sales_tax',
    ];

    public function getTotalAmountAttribute()
    {
        return $this->quantity * $this->rate;
    }

    public function getSalesTaxAmountAttribute()
    {
        return ($this->total_amount * $this->sales_tax_rate) / 100;
    }

    public function getAmountIncludingSalesTaxAttribute()
    {
        return $this->total_amount + $this->sales_tax_amount;
    }
}
