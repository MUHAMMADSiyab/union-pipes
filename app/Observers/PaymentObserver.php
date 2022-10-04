<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function created(Payment $payment)
    {
        if ($payment->transaction_type === 'Credit') {
            $payment->bank()->decrement('balance', $payment->amount);
        }

        if ($payment->transaction_type === 'Debit') {
            $payment->bank()->increment('balance', $payment->amount);
        }
    }

    /**
     * Handle the Payment "updated" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function updated(Payment $payment)
    {
        if ($payment->transaction_type === 'Credit') {
            $payment->bank()->increment('balance', $payment->getOriginal('amount'));
            $payment->bank()->decrement('balance', $payment->amount);
        }

        if ($payment->transaction_type === 'Debit') {
            $payment->bank()->decrement('balance', $payment->getOriginal('amount'));
            $payment->bank()->increment('balance', $payment->amount);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        if ($payment->transaction_type === 'Credit') {
            $payment->bank()->increment('balance', $payment->amount);
        }

        if ($payment->transaction_type === 'Debit') {
            $payment->bank()->decrement('balance', $payment->amount);
        }
    }
}
