<?php

namespace App\Http\Controllers;

use App\Http\Requests\TankRequest;
use App\Models\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TankController extends Controller
{
    /**
     * Get all tanks
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('tank_access');

        $tanks = Tank::query()
            ->with('product')
            ->orderBy('name')
            ->get();

        return response()->json($tanks);
    }

    /**
     * Add a new tank
     *
     * @param  \App\Http\Requests\TankRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TankRequest $request)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_create');

        $tank = Tank::create($request->all());

        return response()->json($tank, 201);
    }

    /**
     * Get a single tank
     * @param App\Tank
     * @return \Illuminate\Http\Response
     */
    public function show(Tank $tank)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_show');

        return response()->json($tank, 201);
    }


    /**
     * Update a tank
     *
     * @param  \App\Http\Requests\TankRequest  $request
     * @param  App\Models\Tank  $tank
     * @return \Illuminate\Http\Response
     */
    public function update(TankRequest $request, Tank $tank)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_edit');

        $updatedTank = tap($tank)->update($request->all());
        return response()->json($updatedTank);
    }

    /**
     * Delete a tank
     *
     * @param  App\Models\Tank $tank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tank $tank)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_delete');

        if ($tank->delete()) {
            return response()->json(["success" =>  "Tank deleted successfully"]);
        }
    }

    /**
     * Delete multiple tanks.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('tank_access');
        Gate::authorize('tank_delete');

        if (Tank::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Tanks deleted successfully"]);
        }
    }
}
