<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerTransactionRequest;
use App\Http\Resources\PartnerTransactionResource;
use App\Models\PartnerTransaction;
use App\Models\Payment;
use App\Services\OrderByService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PartnerTransactionController extends Controller
{
    /**
     * Get all partner transactions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partnerTransactions = PartnerTransaction::query()
            ->where('partner_id', request('partner_id'))
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('title', 'like', '%' . $searchTerm . '%');
            })
            ->when(request('from_date') && request('to_date'), function ($query) {
                $query->whereHas('payment', function ($query) {
                    $query->whereDate('payment_date', '>=', request('from_date'))
                        ->whereDate('payment_date', '<=', request('to_date'));
                });
            })
            ->with(['payment'])
            ->get();

        $ledger = $this->transformToLedger($partnerTransactions);

        return response()->json($ledger);
    }

    private function transformToLedger($partnerTransactions)
    {
        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        $ledgerData = $partnerTransactions->map(function ($transaction) use (&$runningBalance, &$totalDebit, &$totalCredit) {
            $debit = $transaction->type === 'Debit' ? $transaction->amount : 0;
            $credit = $transaction->type === 'Credit' ? $transaction->amount : 0;

            $runningBalance += $debit - $credit;

            // Accumulate totals
            $totalDebit += $debit;
            $totalCredit += $credit;

            return [
                'id' => $transaction->id,
                'title' => $transaction->title,
                'description' => $transaction->description,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $runningBalance,
                'partner' => $transaction->partner,
                'payment' => $transaction->payment,
            ];
        });

        return [
            'data' => new Collection($ledgerData),
            'totals' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ],
        ];
    }



    /**
     * Add a new partner transaction
     *
     * @param  \App\Http\Requests\PartnerTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartnerTransactionRequest $request)
    {
        Gate::authorize('partner_transaction_access');
        Gate::authorize('partner_transaction_create');

        $partner_transaction = PartnerTransaction::create($request->all());

        return response()->json($partner_transaction, 201);
    }

    /**
     * Get a single partner transaction
     * @param App\PartnerTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(PartnerTransaction $partner_transaction)
    {
        Gate::authorize('partner_transaction_access');
        Gate::authorize('partner_transaction_show');

        return response()->json($partner_transaction);
    }


    /**
     * Update a partner transaction
     *
     * @param  \App\Http\Requests\PartnerTransactionRequest  $request
     * @param  App\Models\PartnerTransaction  $partner_transaction
     * @return \Illuminate\Http\Response
     */
    public function update(PartnerTransactionRequest $request, PartnerTransaction $partner_transaction)
    {
        Gate::authorize('partner_transaction_access');
        Gate::authorize('partner_transaction_edit');

        $old_partner_transaction = $partner_transaction->getOriginal();

        $updatedPartnerTransaction = tap($partner_transaction)->update($request->all());

        return response()->json([
            'updated_partner_transaction' =>  $updatedPartnerTransaction,
            'old_partner_transaction' =>  $old_partner_transaction
        ]);
    }

    /**
     * Delete a partner transaction
     *
     * @param  App\Models\PartnerTransaction $partner_transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartnerTransaction $partner_transaction)
    {
        Gate::authorize('partner_transaction_access');
        Gate::authorize('partner_transaction_delete');

        if ($partner_transaction->delete()) {
            Payment::where('model', PartnerTransaction::class)
                ->where('paymentable_id', $partner_transaction->id)
                ->first()
                ->delete(); // delete payment
            return response()->json(["success" =>  "Partner Transaction deleted successfully"]);
        }
    }

    /**
     * Delete multiple partner transactions.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('partner_transaction_access');
        Gate::authorize('partner_transaction_delete');

        foreach ($request->ids as $id) {
            $partner_transaction = PartnerTransaction::find($id);
            $partner_transaction->delete();
            Payment::where('model', PartnerTransaction::class)
                ->where('paymentable_id', $partner_transaction->id)
                ->first()
                ->delete(); // delete payment
        }

        return response()->json(["success" =>  "Partner Transactions deleted successfully"]);
    }
}
