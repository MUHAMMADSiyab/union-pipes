<?php

namespace App\Services;

use App\Models\Payment;

class ChequeClearanceService
{
	public function getUnclearedCheques()
	{
		$data = Payment::query()
			->where('payment_method', 'Cheque')
			->whereNull('cheque_cleared_at')
			->whereNotNull('cheque_due_date')
			->whereDate("cheque_due_date", '<=', now()->format('Y-m-d'))
			->get();

		return $data;
	}
}
