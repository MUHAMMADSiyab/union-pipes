<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispenserRequest;
use App\Models\Dispenser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DispenserController extends Controller
{
    /**
     * Get all companies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('dispenser_access');

        $companies = Dispenser::query()
            ->with('tank')
            ->orderBy('name')
            ->get();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\DispenserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DispenserRequest $request)
    {
        Gate::authorize('dispenser_access');
        Gate::authorize('dispenser_create');

        $dispenser = Dispenser::create($request->all());

        return response()->json($dispenser, 201);
    }

    /**
     * Get a single dispenser
     *
     * @param  int $dispenser (id)
     * @return \Illuminate\Http\Response
     */
    public function show(Dispenser $dispenser)
    {
        Gate::authorize('dispenser_access');
        Gate::authorize('dispenser_show');

        return response()->json($dispenser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\DispenserRequest  $request
     * @param  App\Models\Dispenser $dispenser
     * @return \Illuminate\Http\Response
     */
    public function update(DispenserRequest $request, Dispenser $dispenser)
    {
        Gate::authorize('dispenser_access');
        Gate::authorize('dispenser_edit');

        $dispenser->update($request->all());

        return response()->json(Dispenser::with('tank')->find($dispenser->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Dispenser $dispenser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dispenser $dispenser)
    {
        Gate::authorize('dispenser_access');
        Gate::authorize('dispenser_delete');

        if ($dispenser->delete()) {
            return response()->json(['success' => 'Dispenser deleted successfully']);
        }
    }

    /**
     * Delete multitple companies 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('dispenser_access');
        Gate::authorize('dispenser_delete');

        foreach ($request->ids as $id) {
            Dispenser::find($id)->delete();
        }

        return response()->json(['success' => 'Companies deleted successfully']);
    }
}
