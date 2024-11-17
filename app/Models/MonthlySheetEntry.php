<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySheetEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'category',
        'amount',
    ];

    public function monthly_sheet(): BelongsTo
    {
        return $this->belongsTo(MonthlySheet::class);
    }
}
