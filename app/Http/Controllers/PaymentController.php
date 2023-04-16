<?php

namespace App\Http\Controllers;

use App\Events\PaymentDeleted;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Salary;
use App\Models\Sell;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public $paymentable, $data;

    /**
     * Get payments
     *
     * @param Request $request
     * @return \Illuminate\Http\Response $response
     */
    public function get_payments(Request $request)
    {
        $payments = Payment::whereModel($request->model)
            ->wherePaymentableId($request->paymentable_id)
            ->orderBy('payment_date')
            ->get();

        return response()->json($payments);
    }

    /**
     * Store a new payment
     *
     * @param PaymentRequest $request
     * @param PaymentService $paymentService
     * @return \Illuminate\Http\Response $response
     */
    public function store(PaymentRequest $request, PaymentService $paymentService)
    {
        if ($payment = $paymentService->addNewPayment($request)) {

            if ($request->model === Purchase::class) {
                $this->paymentable = $this->getPurchase($request->paymentable_id);
            }

            if ($request->model === Sell::class) {
                $this->paymentable = $this->getSell($request->paymentable_id);
            }

            if ($request->model === Salary::class) {
                $this->paymentable = $this->getSalaryRecord($request->paymentable_id);
            }

            return response()->json($this->paymentable, 201);
        }
    }

    /**
     * Get a single payment
     *
     * @param Payment $payment
     * @return \Illuminate\Http\Response $response
     */
    public function show(Payment $payment)
    {
        $data = $payment;

        if ($payment->model === Purchase::class) {
            $data['purchase'] = Purchase::query()
                ->with('company')
                ->select('id', 'company_id')
                ->find($payment->paymentable_id);
        }

        if ($payment->model === Sell::class) {
            $data['sell'] = Sell::query()
                ->with('customer')
                ->select('id', 'customer_id')
                ->find($payment->paymentable_id);
        }

        if ($payment->model === Salary::class) {
            $data['salary'] = Salary::query()
                ->with('employee')
                ->select('id', 'employee_id')
                ->find($payment->paymentable_id);
        }

        return response()->json($data);
    }

    /**
     * Update a payment
     *
     * @param PaymentRequest $request
     * @param PaymentService $paymentService
     * @param Payment $payment
     * @return \Illuminate\Http\Response $response
     */
    public function update(PaymentRequest $request, PaymentService $paymentService, Payment $payment)
    {
        $updatedPayment = $paymentService->editPayment($request, $payment);

        if ($payment->model === Purchase::class) {
            $this->data = [
                'paymentable' => $this->getPurchase($payment->paymentable_id),
                'payment' => $updatedPayment
            ];
        }

        if ($payment->model === Sell::class) {
            $this->data = [
                'paymentable' => $this->getSell($payment->paymentable_id),
                'payment' => $updatedPayment
            ];
        }

        if ($payment->model === Salary::class) {
            $this->data = [
                'paymentable' => $this->getSalaryRecord($payment->paymentable_id),
                'payment' => $updatedPayment
            ];
        }

        return response()->json($this->data);
    }

    /**
     * Delete a payment
     *
     * @param PaymentService $paymentService
     * @param Payment $payment
     * @return \Illuminate\Http\Response $response
     */
    public function destroy(PaymentService $paymentService, Payment $payment)
    {
        if ($payment->model === Purchase::class) {
            event(new PaymentDeleted($payment->id, $payment->paymentable_id));
        }

        if ($paymentService->deletePayment($payment)) {
            if ($payment->model === Purchase::class) {
                $this->paymentable = $this->getPurchase($payment->paymentable_id);
            }

            if ($payment->model === Sell::class) {
                $this->paymentable = $this->getSell($payment->paymentable_id);
            }

            if ($payment->model === Salary::class) {
                $this->paymentable = $this->getSalaryRecord($payment->paymentable_id);
            }

            return response()->json($this->paymentable);
        }
    }

    /**
     * Get purchase record
     *
     * @param integer $paymentable_id
     * @return App\Models\Purchase $purchase
     */
    public function getPurchase(int $paymentable_id)
    {
        return Purchase::query()
            ->with([
                'company',
                'purchased_items',
                'purchased_items.purchase_item',
            ])
            ->find($paymentable_id);
    }

    /**
     * Get sell record
     *
     * @param integer $paymentable_id
     * @return App\Models\Sell $sell
     */
    public function getSell(int $paymentable_id)
    {
        return Sell::query()
            ->with([
                'customer',
                'sold_items.product',
                'returned_items.product',
            ])
            ->find($paymentable_id);
    }

    /**
     * Get salary record
     *
     * @param integer $paymentable_id
     * @return App\Models\Salary $salary
     */
    public function getSalaryRecord(int $paymentable_id)
    {
        return Salary::with('payments')->find($paymentable_id);
    }
}
