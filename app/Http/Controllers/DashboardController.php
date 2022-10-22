<?php

namespace App\Http\Controllers;

use App\Models\Tank;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $sell_totals = $dashboardService->getSellTotals();
        $rates_history = $dashboardService->getRatesHistory();
        $sixMonthUtilities = $dashboardService->getLastSixMonthUtilities();
        $tanks = Tank::query()
            ->with('product')
            ->get()
            ->map(function ($tank) {
                return array_merge([
                    'color' => '#' . substr(md5(rand()), 0, 6),
                ], $tank->toArray());
            });

        return response()->json(compact(
            'sell_totals',
            'rates_history',
            'sixMonthUtilities',
            'tanks'
        ));
    }
}
