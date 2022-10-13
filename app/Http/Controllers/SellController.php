<?php

namespace App\Http\Controllers;

use App\Events\SellReadingsUpdated;
use App\Http\Requests\FinalReadingRequest;
use App\Http\Requests\SellRequest;
use App\Models\Sell;
use App\Models\SellReading;
use App\Services\SellService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SellController extends Controller
{
    /**
     * Get all sells
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('sell_access');

        $sells = Sell::orderBy('sell_date', 'desc')->get();

        return response()->json($sells);
    }

    /**
     * Get a single sell
     *
     * @param App\Models\Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function show(Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_show');

        $initial_readings = $sell->initial_readings()
            ->with([
                'meter',
                'meter.dispenser',
                'meter.dispenser.tank',
                'meter.dispenser.tank.product',
            ])
            ->get()
            ->groupBy('meter.dispenser.id')
            ->values()
            ->all();
        $final_readings = $sell->final_readings()
            ->with([
                'meter',
                'meter.dispenser',
                'meter.dispenser.tank',
                'meter.dispenser.tank.product',
            ])
            ->get()
            ->groupBy('meter.dispenser.id')
            ->values()
            ->all();

        return response()->json(compact('sell', 'initial_readings', 'final_readings'));
    }

    /**
     * Get sell's final readings
     *
     * @param Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function get_sell_final_readings(Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_show');

        return response()->json($sell->final_readings()->with([
            'meter',
            'meter.dispenser',
            'meter.dispenser.tank',
            'meter.dispenser.tank.product',
        ])->get()->groupBy('meter.dispenser.id')->values()->all());
    }

    /**
     * Store the sell and its initial readings
     *
     * @param SellRequest $request
     * @param SellService $sellService
     * @return \Illuminate\Http\Response
     */
    public function store(SellRequest $request, SellService $sellService)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_create');

        try {
            DB::beginTransaction();

            $sell = Sell::create($request->only([
                'sell_date',
                'petrol_price',
                'diesel_price',
            ]));

            foreach ($request->initial_readings as $reading) {
                $sellService->saveReadings($sell, $reading, false);
                $sellService->saveReadings($sell, $reading, true);
            }

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Update sell final readings 
     *
     * @param FinalReadingRequest $request
     * @param Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function update_final_readings(FinalReadingRequest $request, Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_edit');

        foreach ($request->final_readings as $reading) {
            foreach ($reading['meters'] as $meter) {
                $sell_reading = SellReading::where('sell_id', $sell->id)
                    ->where('meter_id', $meter['id'])
                    ->where('final_reading', true)
                    ->first();

                $sell_reading->update(['value' => $meter['value']]);
            }
        }

        // Update tanks quantity
        SellReadingsUpdated::dispatch($request->final_readings, $sell, 'final_reading_update');

        return response()->json($sell);
    }

    /**
     * Update the sell with initial readings
     *
     * @param SellRequest $request
     * @param Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function update(SellRequest $request, Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_edit');

        try {
            DB::beginTransaction();

            $sell->update(['sell_date' => $request->sell_date]);

            foreach ($request->initial_readings as $reading) {
                foreach ($reading['meters'] as $meter) {
                    SellReading::where('sell_id', $sell->id)
                        ->where('meter_id', $meter['id'])
                        ->where('final_reading', false)
                        ->update(['value' => $meter['value']]);
                }
            }

            DB::commit();


            // Update tanks quantity
            SellReadingsUpdated::dispatch($request->initial_readings, $sell, 'sell_edit');

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Get the previous sell's final readings
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function get_previous_sell_readings(Request $request)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_create');

        $request->validate(['date' => 'required|date']);

        $previous_sell = Sell::query()
            ->with('final_readings')
            ->whereDate('sell_date', '<', $request->date)
            ->orderBy('sell_date', 'desc')
            ->firstOrFail();

        return response()->json($previous_sell->final_readings()->with([
            'meter',
            'meter.dispenser',
            'meter.dispenser.tank',
            'meter.dispenser.tank.product',
        ])->get());
    }

    /**
     * Delete a sell
     *
     * @param  App\Models\Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_delete');

        if ($sell->delete()) {
            // Payment::where('paymentable_id', $sell->id)->first()->delete(); // delete payment
            return response()->json(["success" =>  "Sell deleted successfully"]);
        }
    }

    /**
     * Delete multiple sells.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_delete');

        foreach ($request->ids as $id) {
            $sell = Sell::find($id);
            $sell->delete();
            // Payment::where('paymentable_id', $id)->first()->delete(); // delete payment
        }

        return response()->json(["success" =>  "Sells deleted successfully"]);
    }
}
