<?php

namespace App\Http\Controllers;

use App\Services\ChequeClearanceService;
use App\Services\DashboardService;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboardService)
    {
        $lastTwelveMonthsExpenses = $dashboardService->getLastTwelveMonthsExpenses();
        $lastTwelveMonthsSells = $dashboardService->getLast12MonthsSells();
        $receivables = (new ReportService)->getReceivablesReport();
        $totals = $dashboardService->getTotals();
        $last30DaysProduction = $dashboardService->getLast30DaysProductionByMachine();
        $unclearedCheques = (new ChequeClearanceService)->getUnclearedCheques();

        return response()->json(compact(
            'lastTwelveMonthsExpenses',
            'lastTwelveMonthsSells',
            'receivables',
            'totals',
            'last30DaysProduction',
            'unclearedCheques'
        ));
    }
}
