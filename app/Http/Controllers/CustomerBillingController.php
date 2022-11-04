<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerBillingRequest;
use App\Models\Account;
use App\Models\Customer;

class CustomerBillingController extends Controller
{
    public function __invoke(CustomerBillingRequest $request)
    {
        $accounts = Account::query()
            ->where('customer_id', $request->customer_id)
            ->where('date', '>=', $request->from_date)
            ->where('date', '<=', $request->to_date)
            ->get();

        $customer = Customer::find($request->customer_id);
        $petrolTotal = $accounts->sum($this->getQuantityTotal('Petrol'));
        $dieselTotal = $accounts->sum($this->getQuantityTotal('Diesel'));
        $petrolAmount = $accounts->sum($this->getAmountTotal('Petrol'));
        $dieselAmount = $accounts->sum($this->getAmountTotal('Diesel'));

        return response()->json(compact(
            'customer',
            'petrolTotal',
            'dieselTotal',
            'petrolAmount',
            'dieselAmount'
        ));
    }

    public function getQuantityTotal($product)
    {
        return function ($account) use ($product) {
            if ($account->product === $product) {
                return $account->product_quantity + 0;
            }
        };
    }

    public function getAmountTotal($product)
    {
        return function ($account) use ($product) {
            if ($account->product === $product) {
                return $account->total_amount + 0;
            }
        };
    }
}
