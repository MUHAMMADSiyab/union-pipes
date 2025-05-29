<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Company;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class LedgerExportService
{
	protected $ledgerService;

	public function __construct(LedgerService $ledgerService)
	{
		$this->ledgerService = $ledgerService;
	}

	public function exportCompanyToPDF($company_id, $from_date = null, $to_date = null)
	{
		$company = Company::findOrFail($company_id);
		$entries = $this->ledgerService->getCompanyLedgerEntries($company_id);

		if ($from_date && $to_date) {
			$entries = array_filter($entries, function ($entry) use ($from_date, $to_date) {
				$entryDate = new \DateTime($entry['date']);
				$fromDate = new \DateTime($from_date);
				$toDate = new \DateTime($to_date);
				$toDate->modify('+1 day');
				return $entryDate >= $fromDate && $entryDate <= $toDate;
			});
		}

		$totalDebit = array_sum(array_column($entries, 'debit'));
		$totalCredit = array_sum(array_column($entries, 'credit'));

		$pdf = Pdf::loadView('exports.ledger', compact('company', 'entries', 'totalDebit', 'totalCredit', 'from_date', 'to_date'))
			->setPaper('a4', 'portrait');

		return Response::streamDownload(
			function () use ($pdf) {
				echo $pdf->output();
			},
			'ledger-' . $company->name . '-' . now()->format('Y-m-d') . '.pdf',
			['Content-Type' => 'application/pdf']
		);
	}

	public function exportCustomerToPDF($customer_id, $from_date = null, $to_date = null)
	{
		$customer = Customer::findOrFail($customer_id);
		$entries = $this->ledgerService->getCustomerLedgerEntries($customer_id);

		if ($from_date && $to_date) {
			$entries = array_filter($entries, function ($entry) use ($from_date, $to_date) {
				$entryDate = new \DateTime($entry['date']);
				$fromDate = new \DateTime($from_date);
				$toDate = new \DateTime($to_date);
				$toDate->modify('+1 day');
				return $entryDate >= $fromDate && $entryDate <= $toDate;
			});
		}

		$totalDebit = array_sum(array_column($entries, 'debit'));
		$totalCredit = array_sum(array_column($entries, 'credit'));

		$pdf = Pdf::loadView('exports.ledger', compact('customer', 'entries', 'totalDebit', 'totalCredit', 'from_date', 'to_date'))
			->setPaper('a4', 'portrait');

		return Response::streamDownload(
			function () use ($pdf) {
				echo $pdf->output();
			},
			'ledger-' . $customer->name . '-' . now()->format('Y-m-d') . '.pdf',
			['Content-Type' => 'application/pdf']
		);
	}

	public function exportBankToPDF($bank_id, $from_date = null, $to_date = null)
	{
		$bank = Bank::findOrFail($bank_id);
		$entries = $this->ledgerService->getBankLedgerEntries($bank_id);

		if ($from_date && $to_date) {
			$entries = array_filter($entries, function ($entry) use ($from_date, $to_date) {
				$entryDate = new \DateTime($entry['date']);
				$fromDate = new \DateTime($from_date);
				$toDate = new \DateTime($to_date);
				$toDate->modify('+1 day');
				return $entryDate >= $fromDate && $entryDate <= $toDate;
			});
		}

		$totalDebit = array_sum(array_column($entries, 'debit'));
		$totalCredit = array_sum(array_column($entries, 'credit'));

		$pdf = Pdf::loadView('exports.ledger', compact('bank', 'entries', 'totalDebit', 'totalCredit', 'from_date', 'to_date'))
			->setPaper('a4', 'portrait');

		return Response::streamDownload(
			function () use ($pdf) {
				echo $pdf->output();
			},
			'ledger-' . $bank->name . '-' . now()->format('Y-m-d') . '.pdf',
			['Content-Type' => 'application/pdf']
		);
	}
}
