<?php

namespace App\Http\Controllers;

use App\Services\LedgerExportService;
use Illuminate\Http\Request;

class LedgerExportController extends Controller
{
    protected $ledgerExportService;

    public function __construct(LedgerExportService $ledgerExportService)
    {
        $this->ledgerExportService = $ledgerExportService;
    }

    public function exportCompanyPDF(Request $request, $company_id)
    {
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        return $this->ledgerExportService->exportCompanyToPDF($company_id, $from_date, $to_date);
    }

    public function exportCustomerPDF(Request $request, $customer_id)
    {
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        return $this->ledgerExportService->exportCustomerToPDF($customer_id, $from_date, $to_date);
    }

    public function exportBankPDF(Request $request, $bank_id)
    {
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        return $this->ledgerExportService->exportBankToPDF($bank_id, $from_date, $to_date);
    }
}
