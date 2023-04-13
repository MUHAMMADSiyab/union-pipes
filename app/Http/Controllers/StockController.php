<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Models\Stock;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
    /**
     * Get all stocks
     *
     * @return \Illuminate\Http\Response
     */
    public function get_stock_item_stocks(int $stock_item_id)
    {
        Gate::authorize('stock_access');

        $stocks = Stock::where('stock_item_id', $stock_item_id)->get();
        return response()->json($stocks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockRequest $request)
    {
        Gate::authorize('stock_access');
        Gate::authorize('stock_create');

        $stock = Stock::create($request->all());
        $stock_item = StockItem::find($stock->stock_item_id);

        return response()->json(compact('stock', 'stock_item'), 201);
    }

    /**
     * Get a single stock
     *
     * @param  App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        Gate::authorize('stock_access');
        Gate::authorize('stock_show');

        return response()->json($stock);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\StockRequest  $request
     * @param  App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function update(StockRequest $request, Stock $stock)
    {
        Gate::authorize('stock_access');
        Gate::authorize('stock_edit');

        $stock->update($request->all());
        $stock = Stock::find($stock->id);
        $stock_item = StockItem::with('stocks')->find($stock->stock_item_id);

        return response()->json(compact('stock', 'stock_item'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        Gate::authorize('stock_access');
        Gate::authorize('stock_delete');

        if ($stock->delete()) {
            $stock_item = StockItem::find($stock->stock_item_id);
            return response()->json($stock_item);
        }
    }

    /**
     * Delete multitple stocks 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('stock_access');
        Gate::authorize('stock_delete');

        foreach ($request->ids as $id) {
            Stock::find($id)->delete();
        }

        return response()->json(['success' => 'Stocks deleted successfully']);
    }
}
