<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'number',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        return $this->date . "_____" . $this->receiver . "_____ 🚛 " . $this->vehicle_no . "_____" . $this->driver_name;
    }

    public function sell(): HasOne
    {
        return $this->hasOne(Sell::class);
    }

    public static function booted()
    {
        static::creating(function ($model) {
            $model->number = GatePass::query()->max('number') + 1;
        });
    }
}
