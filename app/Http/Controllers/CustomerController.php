<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{

    /**
     * Get ledger entries of a customer
     *
     * @param integer $customer_id
     * @param LedgerService $ledgerService
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_ledger_entries(int $customer_id, LedgerService $ledgerService)
    {
        return response()->json($ledgerService->getCustomerLedgerEntries($customer_id));
    }

    public function all()
    {
        return response()->json(Customer::all());
    }


    /**
     * Get all customers
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('customer_access');

        $local = request()->boolean('local');

        $customers = Customer::query()
            ->when($local, function ($q) {
                $q->local();
            })
            ->when(!$local, function ($q) {
                $q->notLocal();
            })
            ->get();


        return response()->json($customers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        Gate::authorize('customer_access');
        Gate::authorize('customer_create');

        $data =  $request->all();
        $data['local'] = $request->boolean('local');

        $customer = Customer::create($data);

        if ($request->hasFile('photo')) {
            $customer->addMediaFromRequest('photo')->toMediaCollection('customers');
        }

        return response()->json($customer, 201);
    }

    /**
     * Get a single customer
     *
     * @param  App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        Gate::authorize('customer_access');
        Gate::authorize('customer_show');

        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CustomerRequest  $request
     * @param  App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        Gate::authorize('customer_access');
        Gate::authorize('customer_edit');

        $data = $request->all();
        $data['local'] = $request->boolean('local');

        $customer->update($data);

        if ($request->hasFile('photo')) {
            if ($media = $customer->getFirstMedia('customers')) {
                $media->delete();
            }
            $customer->addMediaFromRequest('photo')->toMediaCollection('customers');
        }



        return response()->json(Customer::find($customer->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        Gate::authorize('customer_access');
        Gate::authorize('customer_delete');

        if ($customer->delete()) {
            return response()->json(['success' => 'Customer deleted successfully']);
        }
    }
}
