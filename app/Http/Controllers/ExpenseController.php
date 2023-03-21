<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class ExpenseController extends Controller
{
    /**
     * Get all expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('expense_access');

        $expenses = Expense::with(['payment', 'expense_source'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($expenses);
    }

    /**
     * Add a new expense
     *
     * @param  \App\Http\Requests\ExpenseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseRequest $request)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_create');

        $expense = Expense::create($request->all());

        return response()->json($expense, 201);
    }

    /**
     * Get a single expense
     * @param App\Expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_show');

        return response()->json($expense, 201);
    }


    /**
     * Update a expense
     *
     * @param  \App\Http\Requests\ExpenseRequest  $request
     * @param  App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_edit');

        $old_expense = $expense->getOriginal();

        $updatedExpense = tap($expense)->update($request->all());

        return response()->json([
            'updated_expense' =>  $updatedExpense,
            'old_expense' =>  $old_expense
        ]);
    }

    /**
     * Delete a expense
     *
     * @param  App\Models\Expense $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_delete');

        if ($expense->delete()) {
            $expense->payment()->delete(); // delete payment
            return response()->json(["success" =>  "Expense deleted successfully"]);
        }
    }

    /**
     * Delete multiple expenses.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_delete');

        foreach ($request->ids as $id) {
            $expense = Expense::find($id);
            $expense->delete();
            $expense->payment()->delete(); // delete payment
        }

        return response()->json(["success" =>  "Expenses deleted successfully"]);
    }
}
