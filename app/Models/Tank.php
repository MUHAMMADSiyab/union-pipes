<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_id',
        'limit',
        'code',
        'lower_limit',
        'description',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function dispensers(): HasMany
    {
        return $this->hasMany(Dispenser::class);
    }
}
