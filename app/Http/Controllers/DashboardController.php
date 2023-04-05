<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $lastTwelveMonthsExpenses = $dashboardService->getLastTwelveMonthsExpenses();
        $lastTwelveMonthsSells = $dashboardService->getLast12MonthsSells();
        $receivables = (new ReportService)->getReceivablesReport();

        return response()->json(compact(
            'lastTwelveMonthsExpenses',
            'lastTwelveMonthsSells',
            'receivables'
        ));
    }
}
