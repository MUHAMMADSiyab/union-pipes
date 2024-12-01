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

    public function calculateTotals()
    {
        // Initialize totals
        $totalAssets = 0;
        $totalPayables = 0;
        $totalIncome = 0;
        $totalExpenses = 0;

        // Loop through entries to calculate totals based on category
        foreach ($this->entries as $entry) {
            switch ($entry->category) {
                case 'asset':
                    $totalAssets += $entry->amount;
                    break;
                case 'payable':
                    $totalPayables += $entry->amount;
                    break;
                case 'income':
                    $totalIncome += $entry->amount;
                    break;
                case 'expense':
                    $totalExpenses += $entry->amount;
                    break;
            }
        }

        return (($totalAssets - $totalPayables) - $this->previous_month_total) + $totalIncome - $totalExpenses;
    }
}
