<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality',
        'bag',
        'kg',
        'weight',
        'rate',
        'amount'
    ];

    public function raw_material(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
