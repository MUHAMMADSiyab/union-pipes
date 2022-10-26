<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Distribution;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    /**
     * Get all purchases
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('purchase_access');

        $purchases = Purchase::query()
            ->with([
                'company',
                'chamber_readings',
                'chamber_readings.chamber',
                'distributions',
                'distributions.tank',
                'distributions.tank.product',
                'payment',
            ])
            ->get();

        return response()->json($purchases);
    }

    /**
     * Add a new purchase
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_create');

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
     * @param int $purchase(id)
     * @return \Illuminate\Http\Response
     */
    public function show(int $purchase)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_show');

        $purchase = Purchase::with([
            'company',
            'vehicle',
            'chamber_readings',
            'chamber_readings.chamber',
            'distributions',
            'distributions.tank',
            'distributions.tank.product',
            'payment',
            'payment.bank',
        ])->find($purchase);

        return response()->json($purchase);
    }

    /**
     * Update a purchase
     *
     * @param  \App\Http\Requests\PurchaseRequest  $request
     * @param  App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Purchase $purchase, PurchaseRequest $request)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_edit');

        $old_purchase = Purchase::with([
            'chamber_readings',
            'chamber_readings.chamber',
            'distributions',
            'distributions.tank',
            'distributions.tank.product',
            'payment',
        ])->find($purchase->id);

        try {
            DB::beginTransaction();

            $petrol_price = 0;
            $diesel_price = 0;
            $vehicle_charges_petrol_rate = 0;
            $vehicle_charges_diesel_rate = 0;

            if (Str::contains($request->petrol_price, '+')) {
                $exploded = explode('+', $request->petrol_price);
                $price = $exploded[0];
                $rate = $exploded[1];

                $petrol_price = $price;
                $vehicle_charges_petrol_rate = $rate;
            } else {
                $petrol_price = $request->petrol_price;
            }

            if (Str::contains($request->diesel_price, '+')) {
                $exploded = explode('+', $request->diesel_price);
                $price = $exploded[0];
                $rate = $exploded[1];

                $diesel_price = $price;
                $vehicle_charges_diesel_rate = $rate;
            } else {
                $diesel_price = $request->diesel_price;
            }

            $updated_purchase = tap($purchase)->update(
                [...$request->except(['chamber_readings', 'distributions']), ...[
                    'petrol_price' => $petrol_price,
                    'diesel_price' => $diesel_price,
                    'vehicle_charges_petrol_rate' => $vehicle_charges_petrol_rate,
                    'vehicle_charges_diesel_rate' => $vehicle_charges_diesel_rate,
                ]]
            );

            foreach ($request->chamber_readings as $reading) {
                PurchaseReading::where('purchase_id', $purchase->id)
                    ->where('chamber_id', $reading['chamber_id'])
                    ->update([
                        'rod_value' => $reading['rod_value'],
                        'available_quantity' => $reading['available_quantity'],
                        'excess_quantity' => $reading['excess_quantity'],
                    ]);
            }

            foreach ($request->distributions as $distribution) {
                $distribution_record = Distribution::where('purchase_id', $purchase->id)
                    ->where('tank_id', $distribution['tank_id'])
                    ->first();

                $distribution_record->update([
                    'new_stock_quantity' => $distribution['new_stock_quantity']
                ]);
            }


            DB::commit();
            return response()->json(compact('old_purchase', 'updated_purchase'));

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
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_delete');

        if ($purchase->delete()) {
            Payment::where('paymentable_id', $purchase->id)->first()->delete(); // delete payment
            return response()->json(["success" =>  "Purchase deleted successfully"]);
        }
    }

    /**
     * Delete multiple purchases.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_delete');

        foreach ($request->ids as $id) {
            $purchase = Purchase::find($id);
            $purchase->delete();
            Payment::where('paymentable_id', $id)->first()->delete(); // delete payment
        }

        return response()->json(["success" =>  "Purchases deleted successfully"]);
    }
}
