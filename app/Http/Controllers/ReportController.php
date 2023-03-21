<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReportRequest;
use App\Models\Purchase;

class ReportController extends Controller
{
    public function get_purchase_report(PurchaseReportRequest $request)
    {
        $purchases = Purchase::whereIn('company_id', $request->companies)
            ->whereBetween('date', [$request->from_date, $request->to_date])
            ->get();

        return response()->json($purchases);
    }
}
