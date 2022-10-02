<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Distribution;
use App\Models\Purchase;
use App\Models\PurchaseReading;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Add a new purchase
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {
        try {
            DB::beginTransaction();

            $new_purchase = Purchase::create(
                $request->except(['chamber_readings', 'distributions'])
            );

            foreach ($request->chamber_readings as $reading) {
                $new_purchase->chamber_readings()->create($reading);
            }

            foreach ($request->distributions as $distribution) {
                $new_purchase->distributions()->create($distribution);
            }

            DB::commit();

            return response()->json($new_purchase);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single purchase
     * @param App\Purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        return response()->json($purchase, 201);
    }

    /**
     * Update a purchase
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @param  App\Models\Transaction  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        try {
            DB::beginTransaction();

            $updated_purchase = tap($purchase)->update(
                $request->except(['chamber_readings', 'distributions'])
            );

            foreach ($request->chamber_readings as $reading) {
                PurchaseReading::where('purchase_id', $purchase->id)
                    ->where('chamber_id', $reading['chamber_id'])
                    ->update($reading);
            }

            foreach ($request->distributions as $distribution) {
                Distribution::where('purchase_id', $purchase->id)
                    ->where('tank_id', $reading['tank_id'])
                    ->update($distribution);
            }

            DB::commit();

            return response()->json($updated_purchase);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a purchase
     *
     * @param  App\Models\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->delete()) {
            $purchase->payment()->delete(); // delete payment
            return response()->json(["success" =>  "Purchase deleted successfully"]);
        }
    }
}
