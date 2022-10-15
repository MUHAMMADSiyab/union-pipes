<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'product_price',
        'vehicle_no',
        'product',
        'product_quantity',
        'total_amount',
        'date',
    ];

    protected $appends = ['balance'];

    public function getBalanceAttribute()
    {

        $previous_record = $this->where('date', '<', $this->date)
            ->where('customer_id', $this->customer_id)
            ->orderBy('date', 'desc')
            ->first();

        if (!$previous_record) {
            return $this->total_amount - ($this->payment ? $this->payment->amount : 0);
        }

        return ($previous_record->balance + $this->total_amount) -
            ($this->payment ? $this->payment->amount : 0);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'paymentable_id')->where('model', Account::class);
    }
}
