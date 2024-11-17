<?php

namespace App\Http\Controllers;

use App\Http\Requests\RawMaterialRequest;
use App\Models\RawMaterial;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('raw_material_access');

        $raw_materials = RawMaterial::query()->withSum('entries', 'amount')->get();
        return response()->json($raw_materials);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\RawMaterialRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RawMaterialRequest $request)
    {
        Gate::authorize('raw_material_access');
        Gate::authorize('raw_material_create');

        try {
            DB::beginTransaction();

            $data = ['month' => Carbon::parse($request->month . '-01')->lastOfMonth()];
            $raw_material = RawMaterial::query()->create($data);
            foreach ($request->entries as $entry) {
                $raw_material->entries()->create($entry);
            }

            DB::commit();

            return response()->json([
                'message' => 'Raw Material Entries added successfully',
                'raw_material' => RawMaterial::query()
                    ->with('entries')
                    ->find($raw_material->id),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $raw_meterial
     * @return \Illuminate\Http\Response
     */
    public function show(int $raw_material)
    {
        Gate::authorize('raw_material_show');

        $raw_material = RawMaterial::query()
            ->with('entries')
            ->withSum('entries', 'amount')
            ->findOrFail($raw_material);

        return response()->json($raw_material);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\RawMaterialRequest  $request
     * @param  App\Models\RawMaterial  $raw_material
     * @return \Illuminate\Http\Response
     */
    public function update(RawMaterialRequest $request, RawMaterial $raw_material)
    {
        Gate::authorize('raw_material_access');
        Gate::authorize('raw_material_edit');

        try {
            DB::beginTransaction();

            $raw_material->load('entries');

            $data = ['month' => Carbon::parse($request->month . '-01')->lastOfMonth()];
            $raw_material->update($data);
            $raw_material->entries()->delete();

            foreach ($request->entries as $entry) {
                $raw_material->entries()->create($entry);
            }

            DB::commit();



            return response()->json([
                'message' => 'Raw Material Entries updated successfully',
                'raw_material' => RawMaterial::query()
                    ->with('entries')
                    ->find($raw_material->id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\RawMaterial $raw_material
     * @return \Illuminate\Http\Response
     */
    public function destroy(RawMaterial $raw_material)
    {
        Gate::authorize('raw_material_access');
        Gate::authorize('raw_material_delete');

        if ($raw_material->delete()) {
            return response()->json(['message' => 'Raw Material Entries deleted successfully']);
        }
    }

    public function destroy_multiple(Request $request)
    {
        Gate::authorize('raw_material_access');
        Gate::authorize('raw_material_delete');

        if (RawMaterial::whereIn('id', $request->ids)->delete()) {
            return response()->json(["message" =>  "Raw Material Entries deleted successfully"]);
        }
    }
}
