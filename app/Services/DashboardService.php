<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Rate;
use App\Models\Sell;
use App\Models\Utility;
use Carbon\Carbon;

class DashboardService
{

    public function getSellTotals()
    {
        $last_thirty_days_sell = Sell::query()
            ->whereDate('sell_date', '>=', now()->subDays(30))
            ->orderBy('sell_date', 'desc')
            ->get();
        $last_thirty_days_petrol = $last_thirty_days_sell->sum('petrol_sold_amount');
        $last_thirty_days_diesel = $last_thirty_days_sell->sum('diesel_sold_amount');
        $last_thirty_days_all = $last_thirty_days_sell->sum('total_sell_amount');

        return compact(
            'last_thirty_days_sell',
            'last_thirty_days_petrol',
            'last_thirty_days_diesel',
            'last_thirty_days_all'
        );
    }

    public function getRatesHistory()
    {
        $rates = Rate::query()
            ->whereDate('change_date', '>=', now()->subYear(2))
            ->orderBy('change_date', 'desc')
            ->get();

        $dates = $rates->unique('change_date')
            ->sortBy('change_date')
            ->pluck('change_date')
            ->values()
            ->all();
        $rates_history = $rates->groupBy('product.name')->all();

        $data = collect([]);

        foreach ($rates_history as $product => $rate_history) {
            $data->push([
                'product' => $product,
                'rates' => collect($rate_history)->pluck('rate')
            ]);
        }

        return compact('data', 'dates');
    }

    public function getLastSixMonthUtilities()
    {
        $monthsData = Payment::query()
            ->whereModel(Utility::class)
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
