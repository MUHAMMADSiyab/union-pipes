<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'invoice_no',
        'company_id',
        'sales_tax_percentage',
        'category',
        'total_amount',
        'description'
    ];

    protected $appends = [
        'paid',
        'balance',
        'status'
    ];

    public function getPaidAttribute()
    {
        $amountPaid = Payment::whereModel(Purchase::class)
            ->wherePaymentableId($this->id)
            ->sum('amount');

        return $amountPaid;
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->paid;
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


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchased_items()
    {
        return $this->hasMany(PurchasedItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'paymentable_id')
            ->where('model', Purchase::class);
    }
}
