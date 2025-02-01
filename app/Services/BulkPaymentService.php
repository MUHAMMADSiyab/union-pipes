<?php

namespace App\Services;

use App\Models\BulkPayment;
use App\Models\Sell;
use App\Models\Payment;
use App\Models\Purchase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkPaymentService
{
    public function getBulkPayments()
    {
        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $bulk_payments = BulkPayment::query()
            ->with(['customer:id,name', 'company:id,name', 'bank:id,name'])
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'expenses');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('date', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('customer', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('bank', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->paginate(request('itemsPerPage'));

        return $bulk_payments;
    }

    public function processBulkPayment($request)
    {
        try {
            DB::beginTransaction();

            $bulkPaymentData = [
                'payment_method' => $request->payment_method,
                'cheque_no' => $request->cheque_no,
                'cheque_type' => $request->cheque_type,
                'cheque_due_date' => $request->cheque_due_date,
                'bank_id' => $request->bank_id,
                'description' => $request->description,
            ];

            $isAdvance = $request->boolean('is_advance');

            // Create BulkPayment record
            $bulkPayment = BulkPayment::create([
                'type' => $request->type,
                'customer_id' => $request->type === 'Sell' ? $request->customer_id : null,
                'company_id' => $request->type === 'Purchase' ? $request->company_id : null,
                'amount' => $request->amount,
                'date' => $request->date,
                'is_advance' => $isAdvance,
                ...$bulkPaymentData
            ]);

            // Payment cheques
            if ($request->hasFile('cheque_images')) {
                foreach ($request->file('cheque_images') as $cheque_image) {
                    $bulkPayment->addMedia($cheque_image)->toMediaCollection('bulk_payments');
                }
            }


            if ($request->type === 'Sell') {
                if (!$isAdvance) {
                    // Fetch all sells for the customer and filter for those that are 'Partial'
                    $sells = Sell::where('customer_id', $request->customer_id)->get()
                        ->filter(function ($sell) {
                            return $sell->status === 'Partial' || $sell->status === 'Unpaid';
                        })->values()->all();

                    $remainingAmount = $request->amount;
                    $paymentIds = [];

                    foreach ($sells as $sell) {
                        // Calculate the amount needed to settle this sell
                        $neededAmount = $sell->discounted_total_amount - $sell->paid;

                        // Determine the amount to pay towards this sell
                        $paymentAmount = min($remainingAmount, $neededAmount);

                        // Create a payment entry for this sell
                        $payment = Payment::create([
                            'amount' => $paymentAmount,
                            'model' => Sell::class,
                            'transaction_type' => 'Debit',
                            'paymentable_id' => $sell->id,
                            'payment_date' => $request->date,
                            'bulk_payment_id' => $bulkPayment->id,
                            ...$bulkPaymentData
                        ]);


                        $paymentIds[] = $payment->id;

                        // Deduct the payment amount from the remaining amount
                        $remainingAmount -= $paymentAmount;

                        // Break the loop if there's no remaining amount
                        if ($remainingAmount <= 0) {
                            break;
                        }
                    }
                } else {
                    $advanceSellEntry = Sell::query()->create([
                        'date' => $request->date,
                        'customer_id' => $request->customer_id,
                        'category' => 'Advance Payment',
                        'description' => $request->description,
                        'total_amount' => 0,
                    ]);

                    Payment::create([
                        'amount' => $request->amount,
                        'model' => Sell::class,
                        'transaction_type' => 'Debit',
                        'paymentable_id' => $advanceSellEntry->id,
                        // increase 3 seconds to avoid duplicate payment date
                        'payment_date' => date('Y-m-d H:i:s', strtotime($request->date . ' +3 seconds')),
                        'bulk_payment_id' => $bulkPayment->id,
                        'first_payment' => true,
                        ...$bulkPaymentData
                    ]);
                }
            } elseif ($request->type === 'Purchase') {
                if (!$isAdvance) {
                    // Fetch all purchases for the company and filter for those that are 'Partial' or 'Unpaid'
                    $purchases = Purchase::where('company_id', $request->company_id)->get()
                        ->filter(function ($purchase) {
                            return $purchase->status === 'Partial' || $purchase->status === 'Unpaid';
                        })->values()->all();

                    $remainingAmount = $request->amount;
                    $paymentIds = [];

                    foreach ($purchases as $purchase) {
                        // Calculate the amount needed to settle this purchase
                        $neededAmount = $purchase->total_amount - $purchase->paid;

                        // Determine the amount to pay towards this purchase
                        $paymentAmount = min($remainingAmount, $neededAmount);

                        // Create a payment entry for this purchase
                        $payment = Payment::create([
                            'amount' => $paymentAmount,
                            'model' => Purchase::class,
                            'transaction_type' => 'Credit',
                            'paymentable_id' => $purchase->id,
                            'payment_date' => $request->date,
                            'bulk_payment_id' => $bulkPayment->id,
                            ...$bulkPaymentData
                        ]);

                        $paymentIds[] = $payment->id;

                        // Deduct the payment amount from the remaining amount
                        $remainingAmount -= $paymentAmount;

                        // Break the loop if there's no remaining amount
                        if ($remainingAmount <= 0) {
                            break;
                        }
                    }
                } else {
                    $advancePurchaseEntry = Purchase::query()->create([
                        'date' => $request->date,
                        'company_id' => $request->company_id,
                        'category' => 'Advance Payment',
                        'description' => $request->description,
                        'total_amount' => 0,
                    ]);

                    Payment::create([
                        'amount' => $request->amount,
                        'model' => Purchase::class,
                        'transaction_type' => 'Credit',
                        'paymentable_id' => $advancePurchaseEntry->id,
                        // increase 3 seconds to avoid duplicate payment date
                        'payment_date' => date('Y-m-d H:i:s', strtotime($request->date . ' +3 seconds')),
                        'bulk_payment_id' => $bulkPayment->id,
                        'first_payment' => true,
                        ...$bulkPaymentData
                    ]);
                }
            }

            DB::commit();

            return response()->json(compact('bulkPayment'));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
