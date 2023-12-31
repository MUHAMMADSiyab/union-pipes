<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseSource;
use App\Models\Machine;
use App\Models\Purchase;
use App\Models\PurchasedItem;
use App\Models\Sell;
use App\Models\SoldItem;

class ReportService
{
    public function getPurchaseReport($request)
    {
        $purchases = Purchase::with('purchased_items', 'company', 'purchased_items.purchase_item')
            ->where('date', '>=', $request->from_date)
            ->where('date', '<=', $request->to_date)
            ->whereIn('company_id', $request->companies)
            ->get()
            ->groupBy('company_id')
            ->map(function ($company_purchases, $company_id) {
                $purchased_items = $company_purchases->flatMap(function ($purchase) {
                    return $purchase->purchased_items;
                })->groupBy('purchase_item_id')->map(function ($items, $purchase_item_id) {
                    $grand_total = $items->sum('grand_total');
                    $purchase_item_name = $items->first()->purchase_item->name;
                    return [
                        'purchase_item_id' => $purchase_item_id,
                        'purchase_item_name' => $purchase_item_name,
                        'grand_total' => $grand_total
                    ];
                })->values();
                $company_name = $company_purchases->first()->company->name;
                $total_amount = $company_purchases->sum('total_amount');
                $paid = $company_purchases->sum('paid') -
                    $company_purchases->where('total_amount', 0)->sum('paid');
                // $balance = $company_purchases->sum('balance');
                return [
                    'company_id' => $company_id,
                    'company_name' => $company_name,
                    'overall_grand_total' => $total_amount,
                    'paid' => $paid,
                    'balance' => $total_amount - $paid,
                    // 'overall_grand_total' => $purchased_items->sum('grand_total'),
                    'purchased_items' => $purchased_items
                ];
            })->values();

        return $purchases;
    }

    public function getPurchasedItemsReport($request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $companyIds = $request->companies;

        $purchasedItems = PurchasedItem::whereHas('purchase', function ($query) use ($fromDate, $toDate, $companyIds) {
            $query
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->whereIn('company_id', $companyIds)
                ->with('company');
        })
            ->with('purchase')
            ->get();

        $data = $purchasedItems->groupBy('purchase.company.id')->map(function ($items, $companyId) {
            $companyName = $items->first()->purchase->company->name;

            $totalQuantity = $items->sum('quantity');
            $totalTotal = $items->sum('total');
            $totalSalesTax = $items->sum('sales_tax');
            $totalGrandTotal = $items->sum('grand_total');

            $purchasedItemData = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->purchase_item->name,
                    'quantity' => $item->quantity,
                    'rate' => $item->rate,
                    'total' => $item->total,
                    'sales_tax' => $item->sales_tax,
                    'grand_total' => $item->grand_total,
                    'invoice_no' => $item->purchase->invoice_no,
                    'date' => $item->purchase->date,
                ];
            });

            return [
                'company_id' => $companyId,
                'company_name' => $companyName,
                'total_total' => $totalTotal,
                'total_sales_tax' => $totalSalesTax,
                'total_quantity' => $totalQuantity,
                'total_grand_total' => $totalGrandTotal,
                'purchased_items' => $purchasedItemData,
            ];
        })->values()->all();

        $totals['overallQuantity'] = $purchasedItems->sum('quantity');
        $totals['overallTotal'] = $purchasedItems->sum('total');
        $totals['overallSalexTax'] = $purchasedItems->sum('sales_tax');
        $totals['overallGrandTotal'] = $purchasedItems->sum('grand_total');

        return compact('data', 'totals');
    }

    public function getSellReport($request)
    {
        $sells = Sell::with('sold_items', 'customer', 'sold_items.product')
            ->where('date', '>=', $request->from_date)
            ->where('date', '<=', $request->to_date)
            ->whereIn('customer_id', $request->customers)
            ->get()
            ->groupBy('customer_id')
            ->map(function ($customer_sells, $customer_id) {
                $sold_items = $customer_sells->flatMap(function ($purchase) {
                    return $purchase->sold_items;
                })->groupBy('product_id')->map(function ($items, $product_id) {
                    $grand_total = $items->sum('grand_total');
                    $product_name = $items->first()->product->product_full_name;
                    return [
                        'product_id' => $product_id,
                        'product_name' => $product_name,
                        'grand_total' => $grand_total
                    ];
                })->values();
                $customer_name = $customer_sells->first()->customer->name;
                $total_amount = $customer_sells->sum('discounted_total_amount');
                $paid = $customer_sells->sum('paid') -
                    $customer_sells->where('total_amount', 0)->sum('paid');
                // $paid = $customer_sells->where('total_amount', '>', 0)->sum('paid');
                // $balance = $customer_sells->sum('balance');
                return [
                    'customer_id' => $customer_id,
                    'customer_name' => $customer_name,
                    'total_amount' => $customer_sells->sum('total_amount'),
                    'overall_grand_total' => $total_amount,
                    'paid' => $paid,
                    // 'balance' => $balance < 0 ? $balance + $paid : $balance,
                    'balance' => $total_amount - $paid,
                    'sold_items' => $sold_items
                ];
            })->values();

        return $sells;
    }

    public function getSoldItemsReport($request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $customerIds = $request->customers;

        $soldItems = SoldItem::whereHas('sell', function ($query) use ($fromDate, $toDate, $customerIds) {
            $query
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->whereIn('customer_id', $customerIds)
                ->with('customer');
        })
            ->with('sell')
            ->get();

        $data = $soldItems->groupBy('sell.customer.id')->map(function ($items, $customerId) {
            $customerName = $items->first()->sell->customer->name;

            $totalWeight = $items->sum('weight');
            $totalQuantity = $items->sum('quantity');
            $totalTotal = $items->sum('total');
            $totalGrandTotal = $items->sum('grand_total');

            $soldItemData = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->product_full_name,
                    'quantity' => $item->quantity,
                    'rate' => $item->rate,
                    'weight' => $item->weight,
                    'total' => $item->total,
                    'grand_total' => $item->grand_total,
                    'invoice_no' => $item->sell->invoice_no,
                    'date' => $item->sell->date,
                ];
            });

            return [
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'total_total' => $totalTotal,
                'total_weight' => $totalWeight,
                'total_quantity' => $totalQuantity,
                'total_grand_total' => $totalGrandTotal,
                'sold_items' => $soldItemData,
            ];
        })->values()->all();

        $totals['overallWeight'] = $soldItems->sum('weight');
        $totals['overallQuantity'] = $soldItems->sum('quantity');
        $totals['overallTotal'] = $soldItems->sum('total');
        $totals['overallGrandTotal'] = $soldItems->sum('grand_total');

        return compact('data', 'totals');
    }

    public function getReceivablesReport()
    {
        // $customers = Customer::with('sells')->get();

        // $data = $customers->filter(function ($customer) {
        //     $balance = $customer->sells->sum(function ($sell) {
        //         return $sell->getBalanceAttribute();
        //     });
        //     return $balance > 0;
        // })->map(function ($customer) {
        //     $balance = $customer->sells->sum(function ($sell) {
        //         return $sell->getBalanceAttribute();
        //     });
        //     return [
        //         'name' => $customer->name,
        //         'balance' => $balance,
        //     ];
        // })
        //     ->values()
        //     ->all();

        $data = Customer::all()->map(function ($customer) {
            $balance = (new LedgerService)->getCustomerLastBalance($customer->id);
            return [
                'name' => $customer->name,
                'balance' => $balance,
            ];
        })
            ->filter(function ($item) {
                return $item['balance'] != 0;
            })
            ->values()
            ->all();

        return $data;
    }

    public function getPayblesReport()
    {
        // $companies = Company::with('purchases')->get();

        // $data = $companies->filter(function ($company) {
        //     $balance = $company->purchases->sum(function ($purchase) {
        //         return $purchase->getBalanceAttribute();
        //     });
        //     return $balance > 0;
        // })->map(function ($company) {
        //     $balance = $company->purchases->sum(function ($purchase) {
        //         return $purchase->getBalanceAttribute();
        //     });
        //     return [
        //         'name' => $company->name,
        //         'balance' => $balance,
        //     ];
        // })
        //     ->values()
        //     ->all();


        $data = Company::all()->map(function ($company) {
            $balance = (new LedgerService)->getCompanyLastBalance($company->id);
            return [
                'name' => $company->name,
                'balance' => $balance,
            ];
        })
            ->filter(function ($item) {
                return $item['balance'] != 0;
            })
            ->values()
            ->all();

        return $data;
    }

    public function getExpenseReport($request)
    {
        $expenses = Expense::whereHas('expense_source', function ($q) use ($request) {
            $q->whereIn('id', $request->expense_sources);
        })
            ->whereHas('payment', function ($q) use ($request) {
                $q->where('payment_date', '>=', $request->from_date)
                    ->where('payment_date', '<=', $request->to_date);
            })
            ->get();

        $groupedExpenses = $expenses->groupBy('expense_source_id')->map(function ($expenseGroup, $sourceId) {
            $source = ExpenseSource::find($sourceId);
            $total = $expenseGroup->sum('amount');
            $expenses = $expenseGroup->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'date' => $expense->payment->payment_date,
                    'name' => $expense->name,
                    'amount' => $expense->amount
                ];
            });
            return [
                'id' => $source->id,
                'name' => $source->name,
                'total' => $total,
                'expenses' => $expenses
            ];
        });

        $totals['overallTotal'] = $groupedExpenses->sum('total');
        $data = $groupedExpenses->values()->all();

        return compact('data', 'totals');
    }

    public function getMachinesReport($request)
    {
        $machines_productions = Machine::query()
            ->whereHas('productions', function ($q) use ($request) {
                $q->where('date', '>=', $request->from_date)
                    ->where('date', '<=', $request->to_date);
            })
            ->withSum('productions', 'total_weight')
            ->withAvg('productions', 'total_weight')
            ->get();

        return $machines_productions;
    }

    public function getSalariesReport()
    {
        return Employee::whereHas('salaries', function ($query) {
            $query->where('balance', '>', 0);
        })
            ->withSum('salaries', 'balance')->get();
    }
}
