<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionRequest;
use App\Models\Machine;
use App\Models\Production;
use App\Services\OrderByService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    /**
     * Get all productions
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('production_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $productions = Production::query()
            ->with(['machine', 'employee', 'product'])
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'productions');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));
        return response()->json($productions);
    }

    public function search_productions()
    {
        Gate::authorize('production_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $productions = Production::query()
            ->with(['machine', 'employee', 'product'])
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('shift', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('machine', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('employee', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'productions');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

        return response()->json($productions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProductionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductionRequest $request)
    {
        Gate::authorize('production_access');
        Gate::authorize('production_create');

        $production = Production::create($request->all());

        return response()->json($production, 201);
    }

    /**
     * Get a single production
     *
     * @param  App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function show(Production $production)
    {
        Gate::authorize('production_access');
        Gate::authorize('production_show');

        return response()->json($production);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\ProductionRequest  $request
     * @param  App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function update(ProductionRequest $request, Production $production)
    {
        Gate::authorize('production_access');
        Gate::authorize('production_edit');

        $production->update($request->all());

        return response()->json(Production::find($production->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function destroy(Production $production)
    {
        Gate::authorize('production_access');
        Gate::authorize('production_delete');

        if ($production->delete()) {
            return response()->json(['success' => 'Production deleted successfully']);
        }
    }

    /**
     * Delete multitple productions 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('production_access');
        Gate::authorize('production_delete');

        foreach ($request->ids as $id) {
            Production::find($id)->delete();
        }

        return response()->json(['success' => 'Productions deleted successfully']);
    }

    function applyRelationshipOrderBy($query, $orderBy, $orderDirection)
    {
        $splitted = Str::of($orderBy)->explode('.');
        $firstItem = $splitted[0];
        $model = "\\App\Models\\" . ucfirst($firstItem);
        $column = $splitted[1];

        $query->orderBy(
            $model::select($column)
                ->from(Str::plural($firstItem))
                ->whereColumn(
                    Str::plural($firstItem) . ".id",
                    'productions.' . $firstItem . '_id'
                ),
            $orderDirection
        );
    }
}
