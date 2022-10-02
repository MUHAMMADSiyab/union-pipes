<?php

namespace App\Http\Controllers;

use App\Http\Requests\UtilityRequest;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class UtilityController extends Controller
{
    /**
     * Get all utilities
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('utility_access');

        $utilities = Utility::with('payment')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($utilities);
    }

    /**
     * Add a new utility
     *
     * @param  \App\Http\Requests\UtilityRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UtilityRequest $request)
    {
        Gate::authorize('utility_access');
        Gate::authorize('utility_create');

        $utility = Utility::create($request->all());

        return response()->json($utility, 201);
    }

    /**
     * Get a single utility
     * @param App\Utility
     * @return \Illuminate\Http\Response
     */
    public function show(Utility $utility)
    {
        Gate::authorize('utility_access');
        Gate::authorize('utility_show');

        return response()->json($utility, 201);
    }


    /**
     * Update a utility
     *
     * @param  \App\Http\Requests\UtilityRequest  $request
     * @param  App\Models\Utility  $utility
     * @return \Illuminate\Http\Response
     */
    public function update(UtilityRequest $request, Utility $utility)
    {
        Gate::authorize('utility_access');
        Gate::authorize('utility_edit');

        $old_utility = $utility->getOriginal();

        $updatedUtility = tap($utility)->update($request->all());

        return response()->json([
            'updated_utility' =>  $updatedUtility,
            'old_utility' =>  $old_utility
        ]);
    }

    /**
     * Delete a utility
     *
     * @param  App\Models\Utility $utility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Utility $utility)
    {
        Gate::authorize('utility_access');
        Gate::authorize('utility_delete');

        if ($utility->delete()) {
            $utility->payment()->delete(); // delete payment
            return response()->json(["success" =>  "Utility deleted successfully"]);
        }
    }

    /**
     * Delete multiple utilities.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('utility_access');
        Gate::authorize('utility_delete');

        foreach ($request->ids as $id) {
            $utility = Utility::find($id);
            $utility->delete();
            $utility->payment()->delete(); // delete payment
        }

        return response()->json(["success" =>  "Utilities deleted successfully"]);
    }
}
