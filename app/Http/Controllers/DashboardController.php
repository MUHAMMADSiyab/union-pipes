<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $sixMonthExpenses = $dashboardService->getLastSixMonthExpenses();

        return response()->json(compact(
            'sixMonthExpenses',
        ));
    }
}
