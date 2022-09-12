<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_no',
        'make',
        'model',
        'engine_no',
        'chassis_no',
        'contractor_name',
        'calibration_date',
        'receipt_no',
        'validity',
        'capacity',
    ];
    
    public function chambers (): HasMany {
        return $this->hasMany(Chamber::class);
    }
}
