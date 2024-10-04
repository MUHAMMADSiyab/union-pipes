<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sell extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'invoice_no',
        'gate_pass_id',
        'customer_id',
        'sales_tax_percentage',
        'unit',
        'category',
        'discount',
        'description',
        'total_amount'
    ];

    protected $appends = [
        'paid',
        'discount_amount',
        'discounted_total_amount',
        'balance',
        'status',
    ];

    public function getPaidAttribute()
    {
        $amountPaid = Payment::whereModel(Sell::class)
            ->wherePaymentableId($this->id)
            ->sum('amount');

        return $amountPaid;
    }

    public function getDiscountAmountAttribute()
    {
        return $this->total_amount * ($this->discount / 100);
    }

    public function getDiscountedTotalAmountAttribute()
    {
        return $this->total_amount - $this->discount_amount;;
    }

    public function getBalanceAttribute()
    {
        return $this->discounted_total_amount - $this->paid;
    }

    public function getStatusAttribute()
    {
        $status = "";

        if ($this->balance > 0 && $this->paid > 0) {
            $status = "Partial";
        } elseif ($this->balance > 0 && $this->paid === 0) {
            $status = "Unpaid";
        } elseif ($this->balance == 0) {
            $status = "Paid";
        } elseif ($this->balance < 0) {
            $status = "Advance";
        }

        return $status;
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sold_items()
    {
        return $this->hasMany(SoldItem::class, 'sell_id');
    }

    public function returned_items()
    {
        return $this->hasMany(ReturnedSoldItem::class, 'sell_id');
    }

    public function gate_pass(): BelongsTo
    {
        return $this->belongsTo(GatePass::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'paymentable_id')->where('model', Sell::class);
    }

    public function scopeLocal($query)
    {
        return $query->whereHas('customer', function ($q) {
            $q->where('local', true);
        });
    }

    public function scopeNotLocal($query)
    {
        return $query->whereHas('customer', function ($q) {
            $q->where('local', false);
        });
    }
}
