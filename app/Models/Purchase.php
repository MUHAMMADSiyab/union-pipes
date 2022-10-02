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
    ];


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
}
