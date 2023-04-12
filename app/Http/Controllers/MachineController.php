<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineRequest;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MachineController extends Controller
{
    /**
     * Get all machines
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('machine_access');

        $machines = Machine::all();
        return response()->json($machines);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\MachineRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MachineRequest $request)
    {
        Gate::authorize('machine_access');
        Gate::authorize('machine_create');

        $machine = Machine::create($request->all());

        return response()->json($machine, 201);
    }

    /**
     * Get a single machine
     *
     * @param  App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function show(Machine $machine)
    {
        Gate::authorize('machine_access');
        Gate::authorize('machine_show');

        return response()->json($machine);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\MachineRequest  $request
     * @param  App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function update(MachineRequest $request, Machine $machine)
    {
        Gate::authorize('machine_access');
        Gate::authorize('machine_edit');

        $machine->update($request->all());

        return response()->json(Machine::find($machine->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Machine $machine)
    {
        Gate::authorize('machine_access');
        Gate::authorize('machine_delete');

        if ($machine->delete()) {
            return response()->json(['success' => 'Machine deleted successfully']);
        }
    }

    /**
     * Delete multitple machines 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('machine_access');
        Gate::authorize('machine_delete');

        foreach ($request->ids as $id) {
            Machine::find($id)->delete();
        }

        return response()->json(['success' => 'Machines deleted successfully']);
    }
}
