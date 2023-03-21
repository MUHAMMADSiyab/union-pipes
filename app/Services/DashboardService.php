<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardService
{
    public function getLastSixMonthExpenses()
    {
        $monthsData = Payment::query()
            ->whereModel(Expense::class)
            ->whereMonth('payment_date', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(function ($val) {
                return Carbon::parse($val->payment_date)->format('M-Y');
            })
            ->all();

        $monthsTotal = collect([]);

        foreach ($monthsData as $month => $data) {
            $monthsTotal->push([
                'month' => $month,
                'total' => $data->sum('amount')
            ]);
        }

        return $monthsTotal;
    }
}
