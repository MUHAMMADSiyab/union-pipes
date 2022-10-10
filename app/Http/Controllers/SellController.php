<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinalReadingRequest;
use App\Http\Requests\SellRequest;
use App\Models\Sell;
use App\Models\SellReading;
use App\Services\SellService;
use Illuminate\Support\Facades\DB;

class SellController extends Controller
{
    public function index()
    {
        $sells = Sell::with(['initial_readings', 'final_readings'])->get();

        return response()->json($sells);
    }

    public function show(int $sell)
    {
        $sell = Sell::with(['initial_readings', 'final_readings'])
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

    public function update_final_readings(FinalReadingRequest $request, int $sell_id)
    {
        foreach ($request->final_readings as $reading) {
            SellReading::where('sell_id', $sell_id)
                ->where('nozzle_id', $reading['nozzle_id'])
                ->where('final_reading', true)
                ->update(['value' => $reading['value']]);
        }

        return response()->noContent();
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
}
