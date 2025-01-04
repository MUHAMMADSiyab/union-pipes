<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseReportRequest;
use App\Http\Requests\MachineReportRequest;
use App\Http\Requests\PurchaseReportRequest;
use App\Http\Requests\SellReportRequest;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseSource;
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

    public function get_sell_report(SellReportRequest $request, ReportService $reportService)
    {
        $sells = $reportService->getSellReport($request);

        return response()->json($sells);
    }

    public function get_sold_items_report(SellReportRequest $request, ReportService $reportService)
    {
        $sold_items = $reportService->getSoldItemsReport($request);

        return response()->json($sold_items);
    }

    function get_receivables_report(ReportService $reportService)
    {
        $receivables_data = $reportService->getReceivablesReport();

        return response()->json($receivables_data);
    }

    function get_payables_report(ReportService $reportService)
    {
        $payables_data = $reportService->getPayblesReport();

        return response()->json($payables_data);
    }

    function get_expense_report(ExpenseReportRequest $request, ReportService $reportService)
    {
        $expense_data = $reportService->getExpenseReport($request);

        return response()->json($expense_data);
    }

    function get_machines_report(MachineReportRequest $request, ReportService $reportService)
    {
        $machines_productions = $reportService->getMachinesReport($request);

        return response()->json($machines_productions);
    }

    function get_salaries_report(ReportService $reportService)
    {
        $unpaid_salaries = $reportService->getSalariesReport();

        return response()->json($unpaid_salaries);
    }

    function get_stocks_report(MachineReportRequest $request, ReportService $reportService)
    {
        $stocks = $reportService->getStocksReport($request);

        return response()->json($stocks);
    }
}
