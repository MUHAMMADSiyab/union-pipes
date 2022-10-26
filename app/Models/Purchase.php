<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'vehicle_charges_petrol_rate',
        'vehicle_charges_diesel_rate',
        'total_amount',
    ];

    protected $appends = [
        'total_petrol_price',
        'total_diesel_price',
    ];

    public function getTotalPetrolPriceAttribute()
    {
        return $this->petrol_price + $this->vehicle_charges_petrol_rate;
    }

    public function getTotalDieselPriceAttribute()
    {
        return $this->diesel_price + $this->vehicle_charges_diesel_rate;
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
            if (Str::contains(request('petrol_price'), '+')) {
                $exploded = explode('+', request('petrol_price'));
                $price = $exploded[0];
                $rate = $exploded[1];

                $purchase->petrol_price = $price;
                $purchase->vehicle_charges_petrol_rate = $rate;
            } else {
                $purchase->petrol_price = request('petrol_price');
            }

            if (Str::contains(request('diesel_price'), '+')) {
                $exploded = explode('+', request('diesel_price'));
                $price = $exploded[0];
                $rate = $exploded[1];

                $purchase->diesel_price = $price;
                $purchase->vehicle_charges_diesel_rate = $rate;
            } else {
                $purchase->diesel_price = request('diesel_price');
            }
        });
    }
}
