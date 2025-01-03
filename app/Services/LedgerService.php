<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sell;
use Carbon\Carbon;

class LedgerService
{
    public function getCompanyLedgerEntries($company_id, $current_purchase_id = null)
    {
        $purchases = Purchase::query()
            ->when(!is_null($current_purchase_id), function ($q) use ($current_purchase_id) {
                $q->where('id', '!=', $current_purchase_id);
            })
            ->where('company_id', $company_id)
            ->with('purchased_items.purchase_item')
            ->orderBy('date')
            ->get();

        $payments = Payment::where('bank_id', '!=', null)
            ->where('model', Purchase::class)
            ->whereIn('paymentable_id', $purchases->pluck('id')->all())
            ->orderBy('payment_date')
            ->get(['id', 'payment_date', 'payment_method', 'amount', 'description'])
            ->groupBy(fn($item) => date('Y-m-d', strtotime($item['payment_date'])))
            ->map(function ($groupedItems, $date) {
                // return collect([
                //     'id' => $groupedItems->first()->id,
                //     'description' => $groupedItems->first()->description,
                //     'payment_date' => $date . " 12:00:00",
                //     'amount' => $groupedItems->sum('amount'),
                // ]);

                return collect($groupedItems)
                    ->groupBy('payment_method')
                    ->map(function ($groupedByMethod, $payment_method) use ($date) {
                        return [
                            'id' => $groupedByMethod->first()->id,
                            // 'description' => $groupedByMethod->implode('description', " | "),
                            'description' => $groupedByMethod->first()->description,
                            'payment_date' => $groupedByMethod->first()->payment_date,
                            'amount' => $groupedByMethod->sum('amount'),
                            'payment_method' => $payment_method
                        ];
                    })
                    ->values();
            })
            ->collapse();


        $dates = collect(
            array_merge(
                $purchases->pluck('date')->toArray(),
                $payments->pluck('payment_date')->toArray()
            )
        )
            ->sort()
            ->values()
            ->all();

        $entries = [];
        $balance = 0;

        foreach ($dates as $date) {
            $purchase = $purchases->firstWhere('date', $date);
            $payment = $payments->firstWhere('payment_date', $date);

            if ($purchase) {
                // $particular = $purchase->category;
                $invoice_no = $purchase->invoice_no;
                $description = $purchase->description;
            } else {
                // $particular = "Purchase Payment to Party";
                $invoice_no = "";
                $description = $payment['description'];
            }

            $debit = $purchase ? (int)$purchase->total_amount : 0;
            $credit = $payment ? (int)$payment['amount'] : 0;
            $balance += $debit - $credit;

            if ($debit !== 0 || $credit !== 0) {
                $entries[] = [
                    // 'particular' => $particular,
                    'invoice_no' => $invoice_no,
                    'description' => $description,
                    'date' => $date,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                ];
            }
        }

        return $this->filterBetweenDateRange($entries);;
    }

    public function getCompanyLastBalance($company_id, $current_purchase_id = null)
    {
        // $last_record = collect(
        //     $this->getCompanyLedgerEntries(
        //         $company_id,
        //         $current_purchase_id
        //     )
        // )->last(); // orignal one...

        $fromDate = request('from_date');
        $toDate = request('to_date');

        $companyLedgerEntries = $this->getCompanyLedgerEntries($company_id, $current_purchase_id);

        // Apply date filter only if both from_date and to_date are present
        if ($fromDate !== null && $toDate !== null) {
            $filteredEntries = collect($companyLedgerEntries)
                ->whereBetween(
                    'date',
                    [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']
                );
        } else {
            // If either from_date or to_date is missing, include all entries
            $filteredEntries = collect($companyLedgerEntries);
        }

        // Get the last record from the filtered collection
        $lastRecord = $filteredEntries->last();

        return $lastRecord ? $lastRecord['balance'] : 0;
    }
    // -----------------------------------------------------------------------------
    public function getCustomerLedgerEntries($customer_id, $current_sell_id = null)
    {
        $sells = Sell::query()
            ->when(!is_null($current_sell_id), function ($q) use ($current_sell_id) {
                $q->where('id', '!=', $current_sell_id);
            })
            ->where('customer_id', $customer_id)
            ->with('sold_items.product')
            ->orderBy('date')
            ->get();

        $payments = Payment::where('bank_id', '!=', null)
            ->where('model', Sell::class)
            ->whereIn('paymentable_id', $sells->pluck('id')->all())
            ->orderBy('payment_date')
            ->get(['id', 'payment_method', 'payment_date', 'amount', 'description'])
            ->groupBy(fn($item) => date('Y-m-d', strtotime($item['payment_date'])))
            ->map(function ($groupedItems, $date) {
                // return collect([
                //     'id' => $groupedItems->first()->id,
                //     'description' => $groupedItems->first()->description,
                //     'payment_date' => $date . " 12:00:00",
                //     'amount' => $groupedItems->sum('amount'),
                // ]);

                return collect($groupedItems)
                    ->groupBy('payment_method')
                    ->map(function ($groupedByMethod, $payment_method) use ($date) {
                        return [
                            'id' => $groupedByMethod->first()->id,
                            // 'description' => $groupedByMethod->implode('description', " | "),
                            'description' => $groupedByMethod->first()->description,
                            'payment_date' => $groupedByMethod->first()->payment_date,
                            'amount' => $groupedByMethod->sum('amount'),
                            'payment_method' => $payment_method
                        ];
                    })
                    ->values();
            })
            ->collapse();

        $dates = collect(
            array_merge(
                $sells->pluck('date')->toArray(),
                $payments->pluck('payment_date')->toArray()
            )
        )
            ->sort()
            ->values()
            ->all();

        $entries = [];
        $balance = 0;

        foreach ($dates as $date) {
            $sell = $sells->firstWhere('date', $date);
            $payment = $payments->firstWhere('payment_date', $date);

            if ($sell) {
                $discount_description = $sell->category . " (" . $sell->discount . "% discount applied on orignal amount " . $sell->total_amount . ")";
                // $particular = $sell->category === 'Pipe' ? $discount_description : $sell->category;
                $description = $sell->description;
                $invoice_no = $sell->invoice_no;
            } else {
                // $particular = "Sell Payment from Customer";
                $invoice_no = "";
                $description = $payment['description'];
            }

            $debit = $sell ? (int)$sell->discounted_total_amount : 0;
            $credit = $payment ? (int)$payment['amount'] : 0;
            $balance += $debit - $credit;

            if ($debit !== 0 || $credit !== 0) {
                $entries[] = [
                    // 'particular' => $particular,
                    'invoice_no' => $invoice_no,
                    'description' => $description,
                    'date' => date('Y-m-d', strtotime($date)),
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                ];
            }
        }

        return $this->filterBetweenDateRange($entries);
    }
    // ------------------------------------------------------------------------------


    public function getCustomerLastBalance($customer_id, $current_purchase_id = null)
    {
        // $last_record = collect(
        //     $this->getCustomerLedgerEntries(
        //         $customer_id,
        //         $current_purchase_id
        //     )
        // )->last(); // original one..

        $fromDate = request('from_date');
        $toDate = request('to_date');

        $ledgerEntries = $this->getCustomerLedgerEntries($customer_id, $current_purchase_id);

        // Apply date filter only if both from_date and to_date are present
        if ($fromDate !== null && $toDate !== null) {
            $filteredEntries = collect($ledgerEntries)
                ->whereBetween(
                    'date',
                    [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']
                );
        } else {
            // If either from_date or to_date is missing, include all entries
            $filteredEntries = collect($ledgerEntries);
        }

        // Get the last record from the filtered collection
        $lastRecord = $filteredEntries->last();


        return $lastRecord ? $lastRecord['balance'] : 0;
    }

    public function getBankLedgerEntries($bank_id, $returnLastBalance = false)
    {
        $payments = Payment::query()
            ->where('bank_id', $bank_id)
            ->orderBy('payment_date')
            ->get();

        $ledger = [];
        $balance = 0;

        foreach ($payments as $payment) {
            $entry = [
                // 'particular' => explode("\\", $payment->model)[2],
                'description' => $payment->description,
                'date' => $payment->payment_date,
                'debit' => 0,
                'credit' => 0,
                'balance' => $balance,
            ];

            if ($payment->transaction_type === 'Debit') {
                $entry['debit'] = $payment->amount - $payment->discount;
                $balance += $entry['debit'];
            } elseif ($payment->transaction_type === 'Credit') {
                $entry['credit'] = $payment->amount;
                $balance -= $entry['credit'];
            }

            $entry['balance'] = $balance;

            $ledger[] = $entry;
        }

        if ($returnLastBalance) {
            return !is_null(collect($ledger)->last()) ? collect($ledger)->last()['balance'] : 0;
        }

        return $this->filterBetweenDateRange($ledger);
    }

    // public function getBankLedgerEntries($bank_id)
    // {
    //     $payments = Payment::query()
    //         ->where('bank_id', $bank_id)
    //         ->orderBy('payment_date')
    //         ->get();

    //     $groupedPayments = $payments->groupBy(fn($item) => date('Y-m-d', strtotime($item->payment_date)));

    //     $ledger = [];
    //     $balance = 0;

    //     foreach ($groupedPayments as $date => $dailyPayments) {
    //         $debit = 0;
    //         $credit = 0;
    //         $descriptions = [];

    //         foreach ($dailyPayments as $payment) {
    //             $descriptions[] = $payment->description;

    //             if ($payment->transaction_type === 'Debit') {
    //                 $debit += $payment->amount - $payment->discount;
    //                 $balance += $payment->amount - $payment->discount;
    //             } elseif ($payment->transaction_type === 'Credit') {
    //                 $credit += $payment->amount;
    //                 $balance -= $payment->amount;
    //             }
    //         }

    //         $ledger[] = [
    //             'description' => implode("<br>", $descriptions),
    //             'date' => $date,
    //             'debit' => $debit,
    //             'credit' => $credit,
    //             'balance' => $balance,
    //         ];
    //     }

    //     return $this->filterBetweenDateRange($ledger);
    // }


    private function filterBetweenDateRange($entries)
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');

        if ($fromDate && $toDate) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();

            $filteredEntries = collect($entries)->filter(function ($entry) use ($fromDate, $toDate) {
                $entryDate = Carbon::parse($entry['date']);
                return $entryDate->between($fromDate, $toDate);
            })->values()->all();
        } else {
            $filteredEntries = $entries;
        }

        return $filteredEntries;
    }
}
