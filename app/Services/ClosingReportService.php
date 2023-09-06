<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseSource;
use App\Models\Payment;
use App\Models\Production;
use App\Models\Purchase;
use App\Models\PurchasedItem;
use App\Models\Salary;
use App\Models\Sell;
use App\Models\SoldItem;

class ClosingReportService
{
    public function getPayments($fromDate, $toDate)
    {
        $paidToParties = Payment::query()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('model', Purchase::class)
            ->sum('amount');

        $receivedFromCustomers = Payment::query()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('model', Sell::class)
            ->sum('amount');

        return [
            'paid_to_parties' => $paidToParties,
            'received_from_customers' => $receivedFromCustomers,
        ];
    }

    public function getWeights($fromDate, $toDate)
    {
        $purchasedItems = PurchasedItem::query()
            ->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate]);
            })
            ->get();

        $purchasedWeight = $purchasedItems->sum('quantity');
        $purchasedWeightAmount = $purchasedItems->sum('grand_total');

        $soldItems = SoldItem::query()
            ->whereHas('sell', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate]);
            })
            ->get();

        $soldWeight = $soldItems->sum('weight');
        $soldWeightAmount = $soldItems->sum('grand_total');

        return [
            'purchased_weight' => $purchasedWeight,
            'purchased_weight_amount' => $purchasedWeightAmount,
            'sold_weight' => $soldWeight,
            'sold_weight_amount' => $soldWeightAmount,
        ];
    }

    public function getExpensesData($fromDate, $toDate)
    {
        $expensesQuery = Expense::query()
            ->whereHas('payment', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('payment_date', [$fromDate, $toDate]);
            })
            ->get();

        $expenses = $expensesQuery->groupBy('expense_source_id')
            ->map(function ($expenseGroup, $sourceId) {
                $source = ExpenseSource::find($sourceId);
                $total = $expenseGroup->sum('amount');
                return [
                    'name' => $source->name,
                    'total' => $total,
                ];
            });

        $salaries = Payment::query()
            ->where('model', Salary::class)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->sum('amount');

        $expenses->push(['name' => 'Salaries', 'total' => $salaries]);

        $purchasedExpenseItems = PurchasedItem::query()
            ->with('purchase_item')
            ->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate])
                    ->where('category', 'Other');
            })
            ->get();

        $purchased_expense_items = PurchasedItem::query()
            ->with('purchase_item')
            ->whereHas('purchase', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('date', [$fromDate, $toDate])
                    ->where('category', 'Other');
            })
            ->get();


        $grouped_expense_items = $purchased_expense_items->groupBy('purchase_item.name')
            ->map(function ($items, $name) {
                return [
                    'name' => $name,
                    'total' => $items->sum('grand_total'),
                ];
            })->values()->all();

        $expenses->push(...$grouped_expense_items);

        $all_expenses = $expenses->values()->all();
        $expenses_total = $expenses->sum('total');

        return compact('all_expenses', 'expenses_total');
    }

    public function getProductionCostPerUnit($fromDate, $toDate)
    {
        $total_weight_produced = floatval(Production::query()
            ->whereBetween('date', [$fromDate, $toDate])
            ->sum('total_weight')) ?? 0.0;

        $expenses_total = $this->getExpensesData($fromDate, $toDate)['expenses_total'];
        $production_cost_per_unit = $total_weight_produced > 0
            ? ($expenses_total / $total_weight_produced)
            : 0;

        return compact('total_weight_produced', 'expenses_total', 'production_cost_per_unit');
    }
}
