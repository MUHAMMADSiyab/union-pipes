<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemRequest;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockItemController extends Controller
{
    /**
     * Get all stock items
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('stock_item_access');

        $stock_items = StockItem::query()->with('product')->get();
        return response()->json($stock_items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StockItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockItemRequest $request)
    {
        Gate::authorize('stock_item_access');
        Gate::authorize('stock_item_create');

        $stock_item = StockItem::create($request->all());

        return response()->json($stock_item, 201);
    }

    /**
     * Get a single stock item
     *
     * @param  App\Models\StockItem $stock_item
     * @return \Illuminate\Http\Response
     */
    public function show(StockItem $stock_item)
    {
        Gate::authorize('stock_item_access');
        Gate::authorize('stock_item_show');

        $stock_item->load('product');

        return response()->json($stock_item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StockItemRequest  $request
     * @param  App\Models\StockItem $stock_item
     * @return \Illuminate\Http\Response
     */
    public function update(StockItemRequest $request, StockItem $stock_item)
    {
        Gate::authorize('stock_item_access');
        Gate::authorize('stock_item_edit');

        $stock_item->update($request->all());

        return response()->json(StockItem::find($stock_item->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\StockItem $stock_item
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockItem $stock_item)
    {
        Gate::authorize('stock_item_access');
        Gate::authorize('stock_item_delete');

        if ($stock_item->delete()) {
            return response()->json(['success' => 'Stock Item deleted successfully']);
        }
    }

    /**
     * Delete multitple stock items 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('stock_item_access');
        Gate::authorize('stock_item_delete');

        foreach ($request->ids as $id) {
            StockItem::find($id)->delete();
        }

        return response()->json(['success' => 'Stock Item deleted successfully']);
    }
}
