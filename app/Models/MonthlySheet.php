<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlySheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'previous_month_total'
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(MonthlySheetEntry::class);
    }
}
