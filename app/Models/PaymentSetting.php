<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'payment_methods',
        'cheque_types',
        'currency',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'cheque_types' => 'array',
    ];
}
