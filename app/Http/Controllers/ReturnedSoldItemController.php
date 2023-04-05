<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnedSoldItemRequest;
use App\Models\ReturnedSoldItem;
use App\Models\Sell;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReturnedSoldItemController extends Controller
{
    public function store(ReturnedSoldItemRequest $request)
    {
        Gate::authorize('sell_create');
        Gate::authorize('sell_access');

        try {
            DB::beginTransaction();

            foreach ($request->returned_items as $item) {
                ReturnedSoldItem::create($item);
            }

            DB::commit();

            return response()->json(
                Sell::with([
                    'customer',
                    'sold_items',
                    'sold_items.product'
                ])->find($request->returned_items[0]['sell_id'])
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Server Error" . $e->getMessage(), 500);
        }
    }
}
