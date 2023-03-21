<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    /**
     * Get all customers
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('customer_access');

        $customers = Customer::all();
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

        $customer = Customer::create($request->all());

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

        $customer->update($request->all());

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

    /**
     * Delete multitple customers 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('customer_access');
        Gate::authorize('customer_delete');

        foreach ($request->ids as $id) {
            Customer::find($id)->delete();
        }

        return response()->json(['success' => 'Customers deleted successfully']);
    }
}
