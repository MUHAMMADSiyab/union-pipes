<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\Bank;
use App\Models\Payment;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BankController extends Controller
{
    public function get_ledger_entries(int $bank_id, LedgerService $ledgerService)
    {
        return response()->json($ledgerService->getBankLedgerEntries($bank_id));
    }


    /**
     * Get all banks
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('bank_access');

        $banks = Bank::all();
        return response()->json($banks);
    }

    /**
     * Add a new bank
     *
     * @param  \App\Http\Requests\BankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankRequest $request)
    {
        Gate::authorize('bank_access');
        Gate::authorize('bank_create');

        $bank = Bank::create($request->all());

        return response()->json($bank, 201);
    }

    /**
     * Get a single bank
     * @param App\Bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        Gate::authorize('bank_access');
        Gate::authorize('bank_show');

        return response()->json($bank, 201);
    }


    /**
     * Update a bank
     *
     * @param  \App\Http\Requests\BankRequest  $request
     * @param  App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(BankRequest $request, Bank $bank)
    {
        Gate::authorize('bank_access');
        Gate::authorize('bank_edit');

        $updatedBank = tap($bank)->update($request->all());
        return response()->json($updatedBank);
    }

    /**
     * Delete a bank
     *
     * @param  App\Models\Bank $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        Gate::authorize('bank_access');
        Gate::authorize('bank_delete');

        if ($bank->delete()) {
            return response()->json(["success" =>  "Bank deleted successfully"]);
        }
    }

    /**
     * Delete multiple banks.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('bank_access');
        Gate::authorize('bank_delete');

        if (Bank::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Banks deleted successfully"]);
        }
    }
}
