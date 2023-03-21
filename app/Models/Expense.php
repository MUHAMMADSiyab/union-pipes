<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'expense_source_id',
        'description',
    ];

    protected $with = [
        'payment',
    ];

    /**
     * Relations
     */
    public function expense_source()
    {
        return $this->belongsTo(ExpenseSource::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'paymentable_id')->where('model', Expense::class);
    }
}
