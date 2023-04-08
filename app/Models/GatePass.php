<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatePass extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver',
        'vehicle_no',
        'driver_name',
        'date',
        'in_time',
        'out_time',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
