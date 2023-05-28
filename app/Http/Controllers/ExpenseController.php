<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Payment;
use App\Services\OrderByService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $expenses = Expense::with(['payment', 'expense_source'])
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'expenses');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

        return response()->json($expenses);
    }

    public function search_expenses()
    {
        Gate::authorize('expense_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $expenses = Expense::query()
            ->with(['machine', 'employee', 'product'])
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('expense_source', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'expenses');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

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
            Payment::where('model', Expense::class)
                ->where('paymentable_id', $expense->id)
                ->first()
                ->delete(); // delete payment
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
            Payment::where('model', Expense::class)
                ->where('paymentable_id', $expense->id)
                ->first()
                ->delete(); // delete payment
        }

        return response()->json(["success" =>  "Expenses deleted successfully"]);
    }
}
