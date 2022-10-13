<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeterRequest;
use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MeterController extends Controller
{
    /**
     * Get all meters
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('meter_access');

        $meters = Meter::query()
            ->with('dispenser')
            ->orderBy('name')
            ->get();

        return response()->json($meters);
    }

    public function detailed_meters()
    {
        $meters = Meter::with([
            'dispenser',
            'dispenser.tank',
            'dispenser.tank.product',
        ])->get();

        return response()->json($meters);
    }

    /**
     * Add a new meter
     *
     * @param  \App\Http\Requests\MeterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MeterRequest $request)
    {
        Gate::authorize('meter_access');
        Gate::authorize('meter_create');

        $meter = Meter::create($request->all());

        return response()->json($meter, 201);
    }

    /**
     * Get a single meter
     * @param App\Meter
     * @return \Illuminate\Http\Response
     */
    public function show(Meter $meter)
    {
        Gate::authorize('meter_access');
        Gate::authorize('meter_show');

        return response()->json($meter, 201);
    }


    /**
     * Update a meter
     *
     * @param  \App\Http\Requests\MeterRequest  $request
     * @param  App\Models\Meter  $meter
     * @return \Illuminate\Http\Response
     */
    public function update(MeterRequest $request, Meter $meter)
    {
        Gate::authorize('meter_access');
        Gate::authorize('meter_edit');

        $updatedMeter = tap($meter)->update($request->all());
        return response()->json($updatedMeter);
    }

    /**
     * Delete a meter
     *
     * @param  App\Models\Meter $meter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meter $meter)
    {
        Gate::authorize('meter_access');
        Gate::authorize('meter_delete');

        if ($meter->delete()) {
            return response()->json(["success" =>  "Meter deleted successfully"]);
        }
    }

    /**
     * Delete multiple meters.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('meter_access');
        Gate::authorize('meter_delete');

        if (Meter::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Meters deleted successfully"]);
        }
    }
}
