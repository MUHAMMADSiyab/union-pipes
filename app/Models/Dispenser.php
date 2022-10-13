<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispenser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tank_id',
        'description'
    ];

    public function tank(): BelongsTo
    {
        return $this->belongsTo(Tank::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
}
