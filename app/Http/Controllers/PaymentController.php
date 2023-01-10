<?php

namespace App\Http\Controllers;

use App\Events\PaymentAdded;
use App\Events\PaymentDeleted;
use App\Events\PaymentUpdated;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\PurchasedVehicle;
use App\Models\Salary;
use App\Models\SoldVehicle;
use App\Models\VehicleFile;
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
        return response()->json($payment);
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

        if ($payment->model === PurchasedVehicle::class) {
            $this->data = [
                'paymentable' => $this->getPurchasedVehicle($payment->paymentable_id),
                'payment' => $updatedPayment
            ];

            event(new PaymentUpdated(
                $payment->id,
                $payment->paymentable_id,
                $request->investments,
                $request->old_investments
            ));
        }

        if ($payment->model === SoldVehicle::class) {
            $this->data = [
                'paymentable' => $this->getSoldVehicle($payment->paymentable_id),
                'payment' => $updatedPayment
            ];
        }

        if ($payment->model === VehicleFile::class) {
            $this->data = [
                'paymentable' => $this->getVehicleFile($payment->paymentable_id),
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
        if ($payment->model === PurchasedVehicle::class) {
            event(new PaymentDeleted($payment->id, $payment->paymentable_id));
        }

        if ($paymentService->deletePayment($payment)) {
            if ($payment->model === PurchasedVehicle::class) {
                $this->paymentable = $this->getPurchasedVehicle($payment->paymentable_id);
            }

            if ($payment->model === SoldVehicle::class) {
                $this->paymentable = $this->getSoldVehicle($payment->paymentable_id);
            }

            if ($payment->model === VehicleFile::class) {
                $this->paymentable = $this->getVehicleFile($payment->paymentable_id);
            }

            if ($payment->model === Salary::class) {
                $this->paymentable = $this->getSalaryRecord($payment->paymentable_id);
            }

            return response()->json($this->paymentable);
        }
    }

    /**
     * Get salary record
     *
     * @param integer $paymentable_id
     * @return App\Models\Salary $salary
     */
    public function getSalaryRecord(int $paymentable_id)
    {
        $salary = Salary::with('payments')->find($paymentable_id);

        return $salary;
    }
}
