<?php

namespace App\Http\Controllers;

use App\Http\Requests\GatePassRequest;
use App\Models\GatePass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GatePassController extends Controller
{
    /**
     * Get all companies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('gate_pass_access');

        $gate_passes = GatePass::query()
            ->with(['sell' => function ($q) {
                $q->select('id', 'gate_pass_id');
            }])
            ->get();
        return response()->json($gate_passes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\GatePassRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GatePassRequest $request)
    {
        Gate::authorize('gate_pass_access');
        Gate::authorize('gate_pass_create');

        $gate_pass = GatePass::create($request->all());

        return response()->json($gate_pass, 201);
    }

    /**
     * Get a single gate_pass
     *
     * @param  App\Models\GatePass $gate_pass
     * @return \Illuminate\Http\Response
     */
    public function show(GatePass $gate_pass)
    {
        Gate::authorize('gate_pass_access');
        Gate::authorize('gate_pass_show');

        return response()->json($gate_pass);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\GatePassRequest  $request
     * @param  App\Models\GatePass $gate_pass
     * @return \Illuminate\Http\Response
     */
    public function update(GatePassRequest $request, GatePass $gate_pass)
    {
        Gate::authorize('gate_pass_access');
        Gate::authorize('gate_pass_edit');

        $gate_pass->update($request->all());

        return response()->json(GatePass::find($gate_pass->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\GatePass $gate_pass
     * @return \Illuminate\Http\Response
     */
    public function destroy(GatePass $gate_pass)
    {
        Gate::authorize('gate_pass_access');
        Gate::authorize('gate_pass_delete');

        if ($gate_pass->delete()) {
            return response()->json(['success' => 'Gate Pass deleted successfully']);
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
        Gate::authorize('gate_pass_access');
        Gate::authorize('gate_pass_delete');

        foreach ($request->ids as $id) {
            GatePass::find($id)->delete();
        }

        return response()->json(['success' => 'Gate Pass deleted successfully']);
    }

    public function get_no_sell_gate_passes()
    {
        $gate_passes = GatePass::query()
            ->doesntHave('sell')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($gate_passes);
    }
}
