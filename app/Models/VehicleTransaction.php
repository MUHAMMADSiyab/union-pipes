<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vehicle_id',
        'vehicle_charges',
        'expense',
        'driver',
        'purchase_id',
        'date',
    ];

    protected $appends = ['balance'];

    public function getBalanceAttribute()
    {
        $previous_record = $this->where('date', '<', $this->date)
            ->where('vehicle_id', $this->vehicle_id)
            ->orderBy('date', 'desc')
            ->first();

        if (!$previous_record) {
            return $this->vehicle_charges - $this->expense;
        }

        return ($previous_record->balance + $this->vehicle_charges) -
            $this->expense;
    }


    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
