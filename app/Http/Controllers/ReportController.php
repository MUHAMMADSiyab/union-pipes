<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReportRequest;
use App\Models\Purchase;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function get_purchase_report(PurchaseReportRequest $request, ReportService $reportService)
    {
        $purchases = $reportService->getPurchaseReport($request);

        return response()->json($purchases);
    }

    public function get_purchased_items_report(PurchaseReportRequest $request, ReportService $reportService)
    {
        $purchased_items = $reportService->getPurchasedItemsReport($request);

        return response()->json($purchased_items);
    }
}
