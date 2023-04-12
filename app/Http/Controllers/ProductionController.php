<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductionRequest;
use App\Models\Production;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductionController extends Controller
{
    /**
     * Get all productions
     *
     * @return \Illuminate\Http\Response
     */
    public function get_stock_productions(int $stock_item_id)
    {
        Gate::authorize('production_access');

        $productions = Production::where('stock_item_id', $stock_item_id)->get();
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
        $stock_item = StockItem::find($production->stock_item_id);

        return response()->json(compact('production', 'stock_item'), 201);
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
        $production = Production::find($production->id);
        $stock_item = StockItem::with('productions')->find($production->stock_item_id);

        return response()->json(compact('production', 'stock_item'));
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
            $stock_item = StockItem::find($production->stock_item_id);
            return response()->json($stock_item);
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
}
