<?php

namespace App\Http\Controllers;

use App\Http\Requests\NozzleRequest;
use App\Models\Nozzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NozzleController extends Controller
{
    /**
     * Get all nozzles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('nozzle_access');

        $nozzles = Nozzle::query()
            ->with('dispenser')
            ->orderBy('name')
            ->get();

        return response()->json($nozzles);
    }

    public function detailed_nozzles()
    {
        $nozzles = Nozzle::with([
            'dispenser',
            'dispenser.tank',
            'dispenser.tank.product',
        ])->get();

        return response()->json($nozzles);
    }

    /**
     * Add a new nozzle
     *
     * @param  \App\Http\Requests\NozzleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NozzleRequest $request)
    {
        Gate::authorize('nozzle_access');
        Gate::authorize('nozzle_create');

        $nozzle = Nozzle::create($request->all());

        return response()->json($nozzle, 201);
    }

    /**
     * Get a single nozzle
     * @param App\Nozzle
     * @return \Illuminate\Http\Response
     */
    public function show(Nozzle $nozzle)
    {
        Gate::authorize('nozzle_access');
        Gate::authorize('nozzle_show');

        return response()->json($nozzle, 201);
    }


    /**
     * Update a nozzle
     *
     * @param  \App\Http\Requests\NozzleRequest  $request
     * @param  App\Models\Nozzle  $nozzle
     * @return \Illuminate\Http\Response
     */
    public function update(NozzleRequest $request, Nozzle $nozzle)
    {
        Gate::authorize('nozzle_access');
        Gate::authorize('nozzle_edit');

        $updatedNozzle = tap($nozzle)->update($request->all());
        return response()->json($updatedNozzle);
    }

    /**
     * Delete a nozzle
     *
     * @param  App\Models\Nozzle $nozzle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nozzle $nozzle)
    {
        Gate::authorize('nozzle_access');
        Gate::authorize('nozzle_delete');

        if ($nozzle->delete()) {
            return response()->json(["success" =>  "Nozzle deleted successfully"]);
        }
    }

    /**
     * Delete multiple nozzles.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('nozzle_access');
        Gate::authorize('nozzle_delete');

        if (Nozzle::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Nozzles deleted successfully"]);
        }
    }
}
