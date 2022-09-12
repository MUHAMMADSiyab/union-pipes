<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nozzle extends Model
{
    use HasFactory;

    protected $fillable  = [
        'name',
        'code',
        'dispenser_id',
        'description',
    ];

    public function dispenser(): BelongsTo
    {
        return $this->belongsTo(Dispenser::class);
    }
}
