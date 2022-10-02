<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use App\Models\Payment;

class PaymentService
{
    /**
     * Add a new vehicle
     * @param App\Http\Requests\PaymentRequest $request
     * @return App\Models\Payment $payment
     */
    public function addNewPayment(PaymentRequest $request)
    {
        // Check if the payment is first
        $first_payment = !Payment::where('model', $request->model)
            ->where('paymentable_id', $request->paymentable_id)
            ->exists();

        $data = [
            'model' => $request->model,
            'paymentable_id' => $request->paymentable_id,
            'transaction_type' => $request->transaction_type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'cheque_no' => $request->cheque_no,
            'cheque_type' => $request->cheque_type,
            'cheque_due_date' => $request->cheque_due_date,
            'payment_date' => $request->payment_date,
            'bank_id' => $request->bank_id,
            'description' => $request->description,
        ];

        $payment = Payment::create($data);

        // Payment cheques
        if ($request->hasFile('cheque_images')) {
            foreach ($request->cheque_images as $cheque_image) {
                $payment->addMedia($cheque_image)->toMediaCollection('payments');
            }
        }

        if ($payment) {
            return $payment;
        }

        return false;
    }

    /**
     * Edit a payment
     *
     * @param PaymentRequest $request
     * @param Payment $payment
     * @return App\Models\Payment $payment
     */
    public function editPayment(PaymentRequest $request, Payment $payment)
    {
        $data = [
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'cheque_no' => $request->cheque_no,
            'cheque_type' => $request->cheque_type,
            'cheque_due_date' => $request->cheque_due_date,
            'payment_date' => $request->payment_date,
            'bank_id' => $request->bank_id,
            'description' => $request->description,
        ];

        $updatedPayment = tap($payment)->update($data);

        // Payment cheques
        if ($request->hasFile('cheque_images')) {
            foreach ($request->file('cheque_images') as $cheque_image) {
                $updatedPayment->addMedia($cheque_image)->toMediaCollection('payments');
            }
        }

        return $updatedPayment;
    }

    /**
     * Delete a payment
     *
     * @param Payment $payment
     * @return bool
     */
    public function deletePayment(Payment $payment)
    {
        return $payment->delete() === true;
    }
}
