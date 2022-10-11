<?php

namespace App\Http\Controllers;

use App\Events\SellReadingsUpdated;
use App\Http\Requests\FinalReadingRequest;
use App\Http\Requests\SellRequest;
use App\Models\Sell;
use App\Models\SellReading;
use App\Services\SellService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function index()
    {
        $sells = Sell::all();

        return response()->json($sells);
    }

    public function show(int $sell)
    {
        $sell = Sell::with([
            'initial_readings',
            'initial_readings.nozzle',
            'initial_readings.nozzle.dispenser',
            'initial_readings.nozzle.dispenser.tank',
            'initial_readings.nozzle.dispenser.tank.product',
            'final_readings',
            'final_readings.nozzle',
            'final_readings.nozzle.dispenser',
            'final_readings.nozzle.dispenser.tank',
            'final_readings.nozzle.dispenser.tank.product',
        ])
            ->find($sell);

        return response()->json($sell);
    }

    public function get_sell_final_readings(Sell $sell)
    {
        return response()->json($sell->final_readings()->with([
            'nozzle',
            'nozzle.dispenser',
            'nozzle.dispenser.tank',
            'nozzle.dispenser.tank.product',
        ])->get());
    }

    public function store(SellRequest $request, SellService $sellService)
    {
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

    public function update_final_readings(FinalReadingRequest $request, Sell $sell)
    {
        foreach ($request->final_readings as $reading) {
            $sell_reading = SellReading::where('sell_id', $sell->id)
                ->where('nozzle_id', $reading['nozzle_id'])
                ->where('final_reading', true)
                ->first();

            $sell_reading->update(['value' => $reading['value']]);
        }

        // Update tanks quantity
        SellReadingsUpdated::dispatch($request->final_readings, $sell);

        return response()->json($sell);
    }

    public function update(SellRequest $request, Sell $sell)
    {
        try {
            DB::beginTransaction();

            $sell->update(['sell_date' => $request->sell_date]);

            foreach ($request->initial_readings as $reading) {
                SellReading::where('sell_id', $sell->id)
                    ->where('nozzle_id', $reading['nozzle_id'])
                    ->where('final_reading', false)
                    ->update(['value' => $reading['value']]);
            }

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage(), 500);
        }
    }

    public function get_previous_sell_readings(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $previous_sell = Sell::query()
            ->with('final_readings')
            ->whereDate('sell_date', '<', $request->date)
            ->orderBy('sell_date', 'desc')
            ->firstOrFail();

        return response()->json($previous_sell->final_readings);
    }
}
