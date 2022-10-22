<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'invoice_no',
        'company_id',
        'vehicle_id',
        'petrol_quantity',
        'diesel_quantity',
        'petrol_price',
        'diesel_price',
        'total_amount',
        'per_litre_additional_cost',
        'vehicle_charges',
    ];

    protected $appends = ['old_total_per_litre'];

    public function getOldTotalPerLitreAttribute()
    {
        return $this->vehicle_charges / $this->per_litre_additional_cost;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function chamber_readings(): HasMany
    {
        return $this->hasMany(PurchaseReading::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'paymentable_id')->where('model', Purchase::class);
    }

    public static function booted()
    {
        static::creating(function ($purchase) {
            $purchase->petrol_price = $purchase->petrol_price + request('per_litre_additional_cost');
            $purchase->diesel_price = $purchase->diesel_price + request('per_litre_additional_cost');
        });
    }
}
