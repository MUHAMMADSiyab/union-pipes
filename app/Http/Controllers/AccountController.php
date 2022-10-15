<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class AccountController extends Controller
{
    /**
     * Get all accounts of a customer
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function get_customer_accounts(Customer $customer)
    {
        Gate::authorize('account_access');

        $entries = Account::query()
            ->where('customer_id', $customer->id)
            ->with('payment')
            ->orderBy('date')
            ->get();

        $totals = [
            'total_amount' => $entries->sum('total_amount'),
            'total_paid' => $entries->sum('payment.amount'),
            'balance' => $entries->sum('total_amount') - $entries->sum('payment.amount'),
        ];

        return response()->json(compact('entries', 'customer', 'totals'));
    }

    /**
     * Add a new account
     *
     * @param  \App\Http\Requests\AccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        Gate::authorize('account_access');
        Gate::authorize('account_create');

        $account = Account::create($request->all());

        return response()->json($account, 201);
    }

    /**
     * Get a single account
     * @param int $account (id)
     * @return \Illuminate\Http\Response
     */
    public function show(int $account)
    {
        Gate::authorize('account_access');
        Gate::authorize('account_show');

        $account = Account::with(['customer', 'payment'])->find($account);

        return response()->json($account);
    }


    /**
     * Update a account
     *
     * @param  \App\Http\Requests\AccountRequest  $request
     * @param  App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, Account $account)
    {
        Gate::authorize('account_access');
        Gate::authorize('account_edit');

        $old_account = $account->getOriginal();

        $updatedAccount = tap($account)->update($request->all());

        return response()->json([
            'updated_account' =>  $updatedAccount,
            'old_account' =>  $old_account
        ]);
    }

    /**, 
     * Delete a account
     *
     * @param  App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        Gate::authorize('account_access');
        Gate::authorize('account_delete');

        if ($account->delete()) {
            if ($account->payment) {
                $account->payment()->delete(); // delete payment
            }

            return response()->json(["success" =>  "Account deleted successfully"]);
        }
    }

    /**
     * Delete multiple accounts.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('account_access');
        Gate::authorize('account_delete');

        foreach ($request->ids as $id) {
            $account = Account::find($id);
            $account->delete();
            if ($account->payment) {
                $account->payment()->delete(); // delete payment
            }
        }

        return response()->json(["success" =>  "Accounts deleted successfully"]);
    }
}
