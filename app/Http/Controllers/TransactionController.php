<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class TransactionController extends Controller
{
    /**
     * Get all transactions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('transaction_access');

        $transactions = Transaction::with('payment')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($transactions);
    }

    /**
     * Add a new transaction
     *
     * @param  \App\Http\Requests\TransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransactionRequest $request)
    {
        Gate::authorize('transaction_access');
        Gate::authorize('transaction_create');

        $transaction = Transaction::create($request->all());

        return response()->json($transaction, 201);
    }

    /**
     * Get a single transaction
     * @param App\Transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        Gate::authorize('transaction_access');
        Gate::authorize('transaction_show');

        return response()->json($transaction, 201);
    }


    /**
     * Update a transaction
     *
     * @param  \App\Http\Requests\TransactionRequest  $request
     * @param  App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(TransactionRequest $request, Transaction $transaction)
    {
        Gate::authorize('transaction_access');
        Gate::authorize('transaction_edit');

        $old_transaction = $transaction->getOriginal();

        $updatedTransaction = tap($transaction)->update($request->all());

        return response()->json([
            'updated_transaction' =>  $updatedTransaction,
            'old_transaction' =>  $old_transaction
        ]);
    }

    /**
     * Delete a transaction
     *
     * @param  App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        Gate::authorize('transaction_access');
        Gate::authorize('transaction_delete');

        if ($transaction->delete()) {
            Payment::where('model', Transaction::class)
                ->where('paymentable_id', $transaction->id)
                ->first()
                ->delete(); // delete payment
            return response()->json(["success" =>  "Transaction deleted successfully"]);
        }
    }

    /**
     * Delete multiple transactions.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('transaction_access');
        Gate::authorize('transaction_delete');

        foreach ($request->ids as $id) {
            $transaction = Transaction::find($id);
            $transaction->delete();
            Payment::where('model', Transaction::class)
                ->where('paymentable_id', $transaction->id)
                ->first()
                ->delete();
        }

        return response()->json(["success" =>  "Transactions deleted successfully"]);
    }
}
