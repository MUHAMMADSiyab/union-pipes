<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'type',
        'amount',
        'description',
    ];

    protected $with = [
        'payment',
    ];

    /**
     * Relations
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'paymentable_id')->where('model', Transaction::class);
    }
}
