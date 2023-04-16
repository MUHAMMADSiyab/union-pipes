<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClosingReportRequest;
use App\Models\Payment;
use App\Services\ClosingReportService;

class ClosingReportController extends Controller
{
    public function index(ClosingReportRequest $request, ClosingReportService $closingReportService)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $data = [
            'payments' => $closingReportService->getPayments($from_date, $to_date),
            'weights' => $closingReportService->getWeights($from_date, $to_date),
            'expenses' => $closingReportService->getExpensesData($from_date, $to_date),
            'production_cost_per_unit' =>
            $closingReportService->getProductionCostPerUnit($from_date, $to_date),
        ];

        return response()->json($data);
    }
}
