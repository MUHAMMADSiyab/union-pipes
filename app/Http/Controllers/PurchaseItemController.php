<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseItemRequest;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseItemController extends Controller
{
    /**
     * Get all purchase items
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('purchase_item_access');

        $purchase_items = PurchaseItem::all();
        return response()->json($purchase_items);
    }

    /**
     * Add a new purchase item
     *
     * @param  \App\Http\Requests\PurchaseItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseItemRequest $request)
    {
        Gate::authorize('purchase_item_access');
        Gate::authorize('purchase_item_create');

        $purchase_item = PurchaseItem::create($request->all());

        return response()->json($purchase_item, 201);
    }

    /**
     * Get a single purchase item
     * @param App\PurchaseItem
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseItem $purchase_item)
    {
        Gate::authorize('purchase_item_access');
        Gate::authorize('purchase_item_show');

        return response()->json($purchase_item, 201);
    }


    /**
     * Update a purchas item
     *
     * @param  \App\Http\Requests\PurchaseItemRequest  $request
     * @param  App\Models\PurchaseItem  $purchase_item
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseItemRequest $request, PurchaseItem $purchase_item)
    {
        Gate::authorize('purchase_item_access');
        Gate::authorize('purchase_item_edit');

        $updatedPurchaseItem = tap($purchase_item)->update($request->all());
        return response()->json($updatedPurchaseItem);
    }

    /**
     * Delete a purchase item
     *
     * @param  App\Models\PurchaseItem $purchase_item
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseItem $purchase_item)
    {
        Gate::authorize('purchase_item_access');
        Gate::authorize('purchase_item_delete');

        if ($purchase_item->delete()) {
            return response()->json(["success" =>  "Purchase Item deleted successfully"]);
        }
    }

    /**
     * Delete multiple purchase items.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('purchase_item_access');
        Gate::authorize('purchase_item_delete');

        if (PurchaseItem::whereIn('id', $request->ids)->delete()) {
            return response()->json(["success" =>  "Purchase Items deleted successfully"]);
        }
    }
}
