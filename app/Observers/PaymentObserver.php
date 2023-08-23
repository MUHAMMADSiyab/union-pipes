<?php

namespace App\Observers;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\Sell;

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
        if ($payment->bank_id) {
            if ($payment->transaction_type === 'Credit') {
                $payment->bank()->decrement('balance', $payment->amount);
            }

            if ($payment->transaction_type === 'Debit') {
                $payment->bank()->increment('balance', $payment->amount);
            }
        }

        // Salary Payments
        if ($payment->model === Salary::class && !$payment->first_payment) {
            $salary = Salary::find($payment->paymentable_id);
            $total_paid = ($payment->amount + $payment->deducted_amount) - $payment->additional_amount;
            $salary->increment('deducted_amount', $payment->deducted_amount);
            $salary->increment('additional_amount', $payment->additional_amount);
            $salary->increment('total_paid', $payment->amount);
            $salary->decrement('balance', $total_paid);
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
        if ($payment->bank_id) {
            $original_bank = Bank::find($payment->getOriginal('bank_id'));
            if ($payment->transaction_type === 'Credit') {
                $original_bank->increment('balance', $payment->getOriginal('amount'));
                $payment->bank()->decrement('balance', $payment->amount);
            }

            if ($payment->transaction_type === 'Debit') {
                $original_bank->decrement('balance', $payment->getOriginal('amount'));
                $payment->bank()->increment('balance', $payment->amount);
            }
        }

        // Salary Payments
        if ($payment->model === Salary::class && !$payment->first_payment) {
            $salary = Salary::find($payment->paymentable_id);
            $total_paid = ($payment->amount + $payment->deducted_amount) - $payment->additional_amount;
            $total_paid_original =
                ($payment->getOriginal('amount') + $payment->getOriginal('deducted_amount')) - $payment->getOriginal('additional_amount');

            $salary->decrement('deducted_amount', $payment->getOriginal('deducted_amount'));
            $salary->increment('deducted_amount', $payment->deducted_amount);
            $salary->decrement('additional_amount', $payment->getOriginal('additional_amount'));
            $salary->increment('additional_amount', $payment->additional_amount);
            $salary->decrement('total_paid', $payment->getOriginal('amount'));
            $salary->increment('total_paid', $payment->amount);

            $salary->increment('balance', $total_paid_original);
            $salary->decrement('balance', $total_paid);
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
        if ($payment->bank_id) {

            if ($payment->transaction_type === 'Credit') {
                $payment->bank()->increment('balance', $payment->amount);
            }

            if ($payment->transaction_type === 'Debit') {
                $payment->bank()->decrement('balance', $payment->amount);
            }
        }

        // Salary Payments 
        if ($payment->model === Salary::class) {
            $salary = Salary::find($payment->paymentable_id);
            $salary->decrement('total_paid', $payment->getOriginal('amount'));
            $salary->increment('balance', $payment->amount);
        }
    }
}
