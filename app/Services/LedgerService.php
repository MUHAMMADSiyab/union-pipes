<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\PartnerTransaction;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Salary;
use App\Models\Sell;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    # Original Latest #
    // public function getBankLedgerEntries($bank_id, $returnLastBalance = false)
    // {
    //     $payments = Payment::query()
    //         ->where('bank_id', $bank_id)
    //         ->orderBy('payment_date')
    //         ->get();

    //     $ledger = [];
    //     $balance = 0;

    //     foreach ($payments as $payment) {
    //         $entry = [
    //             // 'particular' => explode("\\", $payment->model)[2],
    //             'description' => $payment->description,
    //             'date' => $payment->payment_date,
    //             'debit' => 0,
    //             'credit' => 0,
    //             'balance' => $balance,
    //         ];

    //         if ($payment->transaction_type === 'Debit') {
    //             $entry['debit'] = $payment->amount - $payment->discount;
    //             $balance += $entry['debit'];
    //         } elseif ($payment->transaction_type === 'Credit') {
    //             $entry['credit'] = $payment->amount;
    //             $balance -= $entry['credit'];
    //         }

    //         $entry['balance'] = $balance;

    //         $ledger[] = $entry;
    //     }

    //     if ($returnLastBalance) {
    //         return !is_null(collect($ledger)->last()) ? collect($ledger)->last()['balance'] : 0;
    //     }

    //     return $this->filterBetweenDateRange($ledger);
    // }

    # Updated (without grouping) -- before burraque
    // public function getBankLedgerEntries($bank_id, $returnLastBalance = false)
    // {
    //     $payments = DB::table('payments')
    //         ->leftJoin('purchases', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'purchases.id')
    //                 ->where('payments.model', '=', Purchase::class);
    //         })
    //         ->leftJoin('companies', 'purchases.company_id', '=', 'companies.id')
    //         ->leftJoin('sells', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'sells.id')
    //                 ->where('payments.model', '=', Sell::class);
    //         })
    //         ->leftJoin('customers', 'sells.customer_id', '=', 'customers.id')
    //         ->leftJoin('transactions', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'transactions.id')
    //                 ->where('payments.model', '=', Transaction::class);
    //         })
    //         ->leftJoin('expenses', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'expenses.id')
    //                 ->where('payments.model', '=', Expense::class);
    //         })
    //         ->leftJoin('partner_transactions', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'partner_transactions.id')
    //                 ->where('payments.model', '=', PartnerTransaction::class);
    //         })
    //         ->leftJoin('partners', 'partner_transactions.partner_id', '=', 'partners.id')
    //         ->leftJoin('salaries', function ($join) {
    //             $join->on('payments.paymentable_id', '=', 'salaries.id')
    //                 ->where('payments.model', '=', Salary::class);
    //         })
    //         ->leftJoin('employees', 'salaries.employee_id', '=', 'employees.id')
    //         ->select(
    //             'payments.*',
    //             'customers.id as customer_id',
    //             'customers.name as customer_name',
    //             'companies.id as company_id',
    //             'companies.name as company_name',
    //             'salaries.id as salary_id',
    //             'salaries.month as salary_month',
    //             'employees.id as employee_id',
    //             'employees.name as employee_name',
    //             'transactions.id as transaction_id',
    //             'transactions.title as transaction_title',
    //             'expenses.id as expense_id',
    //             'expenses.name as expense_title',
    //             'partners.id as partner_id',
    //             'partners.name as partner_name'
    //         )
    //         ->where('payments.bank_id', $bank_id)
    //         ->orderBy('payments.payment_date')
    //         ->get();


    //     $ledger = [];
    //     $balance = 0;

    //     foreach ($payments as $payment) {
    //         $particular = '';
    //         $cssClass = 'font-weight-bold indigo--text';

    //         switch ($payment->model) {
    //             case Sell::class:
    //                 $particular = '<span class="' . $cssClass . '">Customer: </span>' . $payment->customer_name ?? '';
    //                 break;
    //             case Purchase::class:
    //                 $particular = '<span class="' . $cssClass . '">Company: </span>' . $payment->company_name ?? '';
    //                 break;
    //             case Transaction::class:
    //                 $particular = '<span class="' . $cssClass . '">Transaction: </span>' . $payment->transaction_title ?? '';
    //                 break;
    //             case PartnerTransaction::class:
    //                 $particular = '<span class="' . $cssClass . '">Partner Transaction: </span>' . $payment->partner_name ?? '';
    //                 break;
    //             case Expense::class:
    //                 $particular = '<span class="' . $cssClass . '">Expense: </span>' . $payment->expense_title ?? '';
    //                 break;
    //             case Salary::class:
    //                 $particular = '<span class="' . $cssClass . '">Salary: </span>' . $payment->employee_name ?? '';
    //                 break;
    //         }

    //         $entry = [
    //             'particular' => $particular,
    //             'description' => $payment->description,
    //             'date' => $payment->payment_date,
    //             'debit' => 0,
    //             'credit' => 0,
    //             'balance' => $balance,
    //         ];

    //         if ($payment->transaction_type === 'Debit') {
    //             $entry['debit'] = $payment->amount - ($payment->discount ?? 0);
    //             $balance += $entry['debit'];
    //         } elseif ($payment->transaction_type === 'Credit') {
    //             $entry['credit'] = $payment->amount;
    //             $balance -= $entry['credit'];
    //         }

    //         $entry['balance'] = $balance;

    //         $ledger[] = $entry;
    //     }

    //     if ($returnLastBalance) {
    //         return !is_null(collect($ledger)->last()) ? collect($ledger)->last()['balance'] : 0;
    //     }

    //     return $this->filterBetweenDateRange($ledger);
    // }

    ### Date Wise Sorted Ledger Entries ###
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


    // Latest from burraque
    public function getBankLedgerEntries($bank_id, $returnLastBalance = false)
    {
        $payments = DB::table('payments')
            ->leftJoin('purchases', function ($join) {
                $join->on('payments.paymentable_id', '=', 'purchases.id')
                    ->where('payments.model', '=', Purchase::class);
            })
            ->leftJoin('companies', 'purchases.company_id', '=', 'companies.id')
            ->leftJoin('sells', function ($join) {
                $join->on('payments.paymentable_id', '=', 'sells.id')
                    ->where('payments.model', '=', Sell::class);
            })
            ->leftJoin('customers', 'sells.customer_id', '=', 'customers.id')
            ->leftJoin('transactions', function ($join) {
                $join->on('payments.paymentable_id', '=', 'transactions.id')
                    ->where('payments.model', '=', Transaction::class);
            })
            ->leftJoin('expenses', function ($join) {
                $join->on('payments.paymentable_id', '=', 'expenses.id')
                    ->where('payments.model', '=', Expense::class);
            })
            ->leftJoin('partner_transactions', function ($join) {
                $join->on('payments.paymentable_id', '=', 'partner_transactions.id')
                    ->where('payments.model', '=', PartnerTransaction::class);
            })
            ->leftJoin('partners', 'partner_transactions.partner_id', '=', 'partners.id')
            ->leftJoin('salaries', function ($join) {
                $join->on('payments.paymentable_id', '=', 'salaries.id')
                    ->where('payments.model', '=', Salary::class);
            })
            ->leftJoin('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select(
                'payments.*',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'companies.id as company_id',
                'companies.name as company_name',
                'salaries.id as salary_id',
                'salaries.month as salary_month',
                'employees.id as employee_id',
                'employees.name as employee_name',
                'transactions.id as transaction_id',
                'transactions.title as transaction_title',
                'expenses.id as expense_id',
                'expenses.name as expense_title',
                'partners.id as partner_id',
                'partners.name as partner_name'
            )
            ->where('payments.bank_id', $bank_id)
            ->orderBy('payments.payment_date')
            ->get();

        $grouped = [];

        foreach ($payments as $payment) {
            $key = null;

            if ($payment->model === Purchase::class && $payment->company_id && $payment->payment_date) {
                $key = 'purchase_' . $payment->company_id . '_' . date('Y-m-d', strtotime($payment->payment_date));
            } elseif ($payment->model === Sell::class && $payment->customer_id && $payment->payment_date) {
                $key = 'sell_' . $payment->customer_id . '_' . date('Y-m-d', strtotime($payment->payment_date));
            }

            if ($key !== null) {
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [];
                }
                $grouped[$key][] = $payment;
            } else {
                $grouped[] = [$payment]; // wrap non-groupable in array
            }
        }


        // Flatten and normalize structure for grouping
        $finalGroups = [];

        foreach ($grouped as $group) {
            if (!is_array($group)) $group = $group->toArray();

            $group = collect($group);
            $first = $group->first();

            $particular = '';
            $cssClass = 'font-weight-bold indigo--text';

            switch ($first->model) {
                case Sell::class:
                    $particular = '<span class="' . $cssClass . '">Customer: </span>' . ($first->customer_name ?? '');
                    break;
                case Purchase::class:
                    $particular = '<span class="' . $cssClass . '">Company: </span>' . ($first->company_name ?? '');
                    break;
                case Transaction::class:
                    $particular = '<span class="' . $cssClass . '">Transaction: </span>' . ($first->transaction_title ?? '');
                    break;
                case PartnerTransaction::class:
                    $particular = '<span class="' . $cssClass . '">Partner Transaction: </span>' . ($first->partner_name ?? '');
                    break;
                case Expense::class:
                    $particular = '<span class="' . $cssClass . '">Expense: </span>' . ($first->expense_title ?? '');
                    break;
                case Salary::class:
                    $particular = '<span class="' . $cssClass . '">Salary: </span>' . ($first->employee_name ?? '');
                    break;
            }

            $groupedDebit = 0;
            $groupedCredit = 0;

            foreach ($group as $payment) {
                if ($payment->transaction_type === 'Debit') {
                    $groupedDebit += $payment->amount - ($payment->discount ?? 0);
                } elseif ($payment->transaction_type === 'Credit') {
                    $groupedCredit += $payment->amount;
                }
            }

            $finalGroups[] = [
                'particular' => $particular,
                'description' => $first->description,
                'date' => $first->payment_date,
                'debit' => $groupedDebit,
                'credit' => $groupedCredit,
                'model' => $first->model, // for sorting if needed
            ];
        }

        // Sort by date to preserve chronological flow
        usort($finalGroups, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));

        // Final ledger with running balance
        $ledger = [];
        $balance = 0;

        foreach ($finalGroups as $entry) {
            $balance += $entry['debit'];
            $balance -= $entry['credit'];
            $entry['balance'] = $balance;
            $ledger[] = $entry;
        }

        if ($returnLastBalance) {
            return !is_null(collect($ledger)->last()) ? collect($ledger)->last()['balance'] : 0;
        }

        return $this->filterBetweenDateRange($ledger);
    }


    ##

    ##






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
