<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseSource;
use App\Models\Sell;
use Carbon\Carbon;

class DashboardService
{

    public function getLastTwelveMonthsExpenses()
    {
        // Calculate the start date as 12 months ago from today
        $startDate = Carbon::now()->subMonths(12);

        // Retrieve expenses that have payments within the last 12 months
        $expenses = Expense::whereHas('payment', function ($q) use ($startDate) {
            $q->where('payment_date', '>=', $startDate);
        })
            ->get();

        // Group the expenses by expense source and month
        $groupedExpenses = $expenses->groupBy(['expense_source_id', function ($expense) {
            return Carbon::parse($expense->payment->payment_date)->format('Y-m');
        }]);

        // Initialize an array to hold the chart data
        $chartData = [];

        $months = [];
        // Loop through each expense source
        foreach ($groupedExpenses as $sourceId => $sourceExpenses) {
            // Find the expense source
            $source = ExpenseSource::find($sourceId);

            // Initialize an array to hold the monthly totals for this expense source
            $monthlyTotals = [];

            // Loop through the last 12 months
            for ($i = 0; $i < 12; $i++) {
                $month = Carbon::now()->subMonths($i);
                $monthString = $month->format('Y-m');

                // Find the expenses for this expense source in this month
                $expensesInMonth = collect([]);

                foreach ($sourceExpenses->get($monthString, []) as $expense) {
                    if ($expense->payment && Carbon::parse($expense->payment->payment_date)->format('Y-m') === $monthString) {
                        $expensesInMonth->push($expense);
                    }
                }

                // Calculate the total for this month
                $totalInMonth = $expensesInMonth->sum('amount');

                // Add the total to the monthly totals array
                $monthlyTotals[$monthString] = $totalInMonth;
                array_push($months, $monthString);
            }

            // Add the expense source and its monthly totals to the chart data array
            $chartData[] = [
                'name' => $source->name,
                'totals' => $monthlyTotals
            ];
        }

        return compact('chartData', 'months');
    }

    public function getLast12MonthsSells()
    {
        $last12Months = collect(range(0, 11))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $sells = Sell::where('date', '>=', $last12Months->last() . '-01')
            ->get()
            ->groupBy(function ($sell) {
                return Carbon::parse($sell->date)->format('Y-m');
            })
            ->map(function ($groupedSells) {
                return $groupedSells->sum('total_amount');
            });

        return $last12Months->mapWithKeys(function ($month) use ($sells) {
            return [$month => $sells->get($month, 0)];
        })->reverse()->toArray();
    }
}
