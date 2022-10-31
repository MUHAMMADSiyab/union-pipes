<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleTransactionRequest;
use App\Models\Vehicle;
use App\Models\VehicleTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class VehicleTransactionController extends Controller
{
    /**
     * Get all vehicle_transactions of a vehicle
     *
     * @param \App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function get_vehicle_transactions(Vehicle $vehicle)
    {
        Gate::authorize('vehicle_transaction_access');

        $entries = VehicleTransaction::query()
            ->where('vehicle_id', $vehicle->id)
            ->with('purchase', function ($q) {
                $q->select('id', 'invoice_no');
            })
            ->orderBy('date')
            ->get();

        $totals = [
            'total_vehicle_charges' => $entries->sum('vehicle_charges'),
            'total_expense' => $entries->sum('expense'),
            'balance' => $entries->sum('vehicle_charges') - $entries->sum('expense'),
        ];

        return response()->json(compact('entries', 'vehicle', 'totals'));
    }

    /**
     * Add a new vehicle_transaction
     *
     * @param  \App\Http\Requests\VehicleTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleTransactionRequest $request)
    {
        Gate::authorize('vehicle_transaction_access');
        Gate::authorize('vehicle_transaction_create');

        $vehicle_transaction = VehicleTransaction::create($request->all());

        return response()->json($vehicle_transaction, 201);
    }

    /**
     * Get a single vehicle_transaction
     * @param App\Models\VehicleTransaction $vehicle_transaction 
     * @return \Illuminate\Http\Response
     */
    public function show(VehicleTransaction $vehicle_transaction)
    {
        Gate::authorize('vehicle_transaction_access');
        Gate::authorize('vehicle_transaction_show');

        return response()->json($vehicle_transaction);
    }


    /**
     * Update a vehicle_transaction
     *
     * @param  \App\Http\Requests\VehicleTransactionRequest  $request
     * @param  App\Models\VehicleTransaction  $vehicle_transaction
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleTransactionRequest $request, VehicleTransaction $vehicle_transaction)
    {
        Gate::authorize('vehicle_transaction_access');
        Gate::authorize('vehicle_transaction_edit');

        $vehicle_transaction->update($request->all());

        return response()->noContent();
    }

    /**, 
     * Delete a vehicle_transaction
     *
     * @param  App\Models\VehicleTransaction $vehicle_transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleTransaction $vehicle_transaction)
    {
        Gate::authorize('vehicle_transaction_access');
        Gate::authorize('vehicle_transaction_delete');

        if ($vehicle_transaction->delete()) {
            if ($vehicle_transaction->payment) {
                $vehicle_transaction->payment()->delete(); // delete payment
            }

            return response()->json(["success" =>  "VehicleTransaction deleted successfully"]);
        }
    }

    /**
     * Delete multiple vehicle_transactions.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('vehicle_transaction_access');
        Gate::authorize('vehicle_transaction_delete');

        foreach ($request->ids as $id) {
            $vehicle_transaction = VehicleTransaction::find($id);
            $vehicle_transaction->delete();
            if ($vehicle_transaction->payment) {
                $vehicle_transaction->payment()->delete(); // delete payment
            }
        }

        return response()->json(["success" =>  "VehicleTransactions deleted successfully"]);
    }
}
