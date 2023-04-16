<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseSource;
use App\Models\Machine;
use App\Models\Payment;
use App\Models\Production;
use App\Models\PurchasedItem;
use App\Models\Sell;
use App\Models\SoldItem;
use App\Models\Stock;
use App\Models\StockItem;
use Carbon\Carbon;

class DashboardService
{

    public function getTotals()
    {
        $materials_purchased = PurchasedItem::query()->sum('grand_total');
        $pipe_sold = SoldItem::query()->sum('grand_total');
        $stock_weight_available = StockItem::first()->available_quantity;
        $total_expenses = Payment::where('model', Expense::class)->sum('amount');

        return compact('materials_purchased', 'pipe_sold', 'stock_weight_available', 'total_expenses');
    }

    public function getLast30DaysProductionByMachine()
    {
        $start_date = now()->subDays(29)->startOfDay();
        $end_date = now()->endOfDay();

        $productions = Production::with('machine')
            ->whereBetween('date', [$start_date, $end_date])
            ->selectRaw('date, SUM(total_weight) as total_weight, machine_id')
            ->groupBy('date', 'machine_id')
            ->orderBy('date', 'ASC')
            ->get();

        $days = [];
        $productionByMachine = [];

        // Initialize all days with 0 production for each machine
        for ($i = 0; $i < 30; $i++) {
            $date = now()->subDays($i)->format('d-M');
            $days[$date] = $date;
            foreach (Machine::all() as $machine) {
                $machineId = $machine->id;
                $machineName = $machine->name;
                if (!isset($productionByMachine[$machineId])) {
                    $productionByMachine[$machineId] = [
                        'name' => $machineName,
                        'data' => [],
                    ];
                }
                $productionByMachine[$machineId]['data'][$date] = 0;
            }
        }

        // Update production data for days with actual production data
        foreach ($productions as $p) {
            $machineId = $p->machine_id;
            $machineName = $p->machine->name;
            $date = Carbon::parse($p->date)->format('d-M');
            $totalWeight = $p->total_weight;

            if (!isset($productionByMachine[$machineId])) {
                $productionByMachine[$machineId] = [
                    'name' => $machineName,
                    'data' => [],
                ];
            }

            $productionByMachine[$machineId]['data'][$date] = $totalWeight;
            $days[$date] = $date;
        }

        $chartData = [
            'days' => array_values($days),
            'productionByMachine' => array_values($productionByMachine),
        ];

        return $chartData;
    }



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
