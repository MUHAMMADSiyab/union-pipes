<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseSourceRequest;
use App\Models\ExpenseSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpenseSourceController extends Controller
{
    /**
     * Get all expense_sources
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('expense_access');

        $expense_sources = ExpenseSource::all();
        return response()->json($expense_sources);
    }

    /**
     * Add a new expense_source
     *
     * @param  \App\Http\Requests\ExpenseSourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseSourceRequest $request)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_create');

        $expense_source = ExpenseSource::create($request->all());

        return response()->json($expense_source, 201);
    }

    /**
     * Get a single expense_source
     * @param App\ExpenseSource
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseSource $expense_source)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_show');

        return response()->json($expense_source, 201);
    }


    /**
     * Update a expense_source
     *
     * @param  \App\Http\Requests\ExpenseSourceRequest  $request
     * @param  App\Models\ExpenseSource  $expense_source
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseSourceRequest $request, ExpenseSource $expense_source)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_edit');

        $updatedExpenseSource = tap($expense_source)->update($request->all());
        return response()->json($updatedExpenseSource);
    }

    /**
     * Delete a expense_source
     *
     * @param  App\Models\ExpenseSource $expense_source
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseSource $expense_source)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_delete');

        if ($expense_source->delete()) {
            return response()->json(["success" =>  "ExpenseSource deleted successfully"]);
        }
    }

    /**
     * Delete multiple expense_sources.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('expense_access');
        Gate::authorize('expense_delete');

        if (ExpenseSource::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "ExpenseSources deleted successfully"]);
        }
    }
}
