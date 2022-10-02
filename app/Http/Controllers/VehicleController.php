<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VehicleController extends Controller
{
    /**
     * Get all vehicles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('tank_access');

        $vehicles = Vehicle::query()->withCount('chambers')->get();
        return response()->json($vehicles);
    }

    /**
     * Add a new vehicle
     *
     * @param  \App\Http\Requests\VehicleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_create');

        try {
            DB::beginTransaction();

            $vehicle = Vehicle::query()->create($request->except(['chambers']));

            foreach ($request->chambers as $chamber) {
                $vehicle->chambers()->create($chamber);
            }

            DB::commit();

            return response()->noContent(201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single vehicle
     * @param int $vehicle (id)
     * @return \Illuminate\Http\Response
     */
    public function show(int $vehicle)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_show');

        $vehicle = Vehicle::with('chambers')->find($vehicle);
        return response()->json($vehicle, 201);
    }

    /**
     * Update a vehicle
     *
     * @param  \App\Http\Requests\VehicleRequest  $request
     * @param  App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Vehicle $vehicle, VehicleRequest $request)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_edit');

        try {
            DB::beginTransaction();

            $vehicle = tap($vehicle)->update($request->except(['chambers']));

            foreach ($request->chambers as $chamber) {
                $vehicle->chambers()->updateOrCreate(['id' => $chamber['id']], [
                    'capacity' => $chamber['capacity'],
                    'dip_value' => $chamber['dip_value'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a vehicle
     *
     * @param  App\Models\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        Gate::authorize('vehicle_access');
        Gate::authorize('vehicle_delete');

        if ($vehicle->delete()) {
            return response()->json(["success" =>  "Vehicle deleted successfully"]);
        }
    }

    public function getVehicleChambers(Vehicle $vehicle)
    {
        return response()->json($vehicle->chambers);
    }
}
