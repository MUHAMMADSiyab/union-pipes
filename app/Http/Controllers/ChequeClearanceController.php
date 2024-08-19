<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class ChequeClearanceController extends Controller
{
    public function markAsCleared(Payment $payment)
    {
        if ($payment->update(['cheque_cleared_at' => now()])) {
            return response()->json(['id' => $payment->id]);
        }
    }

    public function markAllAsCleared()
    {
        Payment::query()
            ->where('payment_method', 'Cheque')
            ->whereNull('cheque_cleared_at')
            ->whereDate("cheque_due_date", '<=', now()->format('Y-m-d'))
            ->update(['cheque_cleared_at' => now()]);
    }
}
