<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellRequest;
use App\Models\Payment;
use App\Models\Sell;
use App\Models\SoldItem;
use App\Models\StockItem;
use App\Services\LedgerService;
use App\Services\OrderByService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SellController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('sell_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $local = request()->boolean('local');
        $sells = Sell::query()
            ->with([
                'customer',
                'sold_items.product',
                'returned_items.product',
            ])
            ->when($local, function ($q) {
                $q->local();
            })
            ->when(!$local, function ($q) {
                $q->notLocal();
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'sells');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

        return response()->json($sells);
    }

    public function search_sells()
    {
        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';
        $local = request()->boolean('local');


        $query = Sell::query()
            ->with([
                'customer',
                'sold_items.product',
                'returned_items.product',
            ])
            ->when(!empty(request('search')), function ($q) {
                $q->where(function ($query) {
                    $searchTerm = request('search');
                    $query->where('date', 'like', '%' . $searchTerm . '%')
                        ->orWhere('invoice_no', 'like', '%' . $searchTerm . '%')
                        ->orWhere('category', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('customer', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', '%' . $searchTerm . '%');
                        });
                });
            })
            ->when(!empty(request('date')), function ($q) {
                $q->whereDate('date', request('date'));
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'sells');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->when($local, function ($q) {
                $q->local();
            })
            ->when(!$local, function ($q) {
                $q->notLocal();
            });


        if (!empty(request('search')) || !empty(request('date'))) {
            $sells = $query->paginate(2000);
        } else {
            $sells = $query->paginate(request('itemsPerPage'));
        }

        return response()->json($sells);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SellRequest $request)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_create');

        try {
            DB::beginTransaction();

            $sell = Sell::create($request->only([
                'date',
                'invoice_no',
                'gate_pass_id',
                'customer_id',
                'sales_tax_percentage',
                'category',
                'unit',
                'discount',
                'total_amount',
                'description',
                'stock_item_id'
            ]));

            if ($request->category === 'Pipe') {
                foreach ($request->items as $item) {
                    $soldItem = $sell->sold_items()->create($item);

                    if (is_null($sell->stock_item_id)) {
                        $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
                    } else {
                        $stock_item = StockItem::query()->find($sell->stock_item_id);
                    }

                    if (!is_null($stock_item)) {
                        $stock_item->decrement('available_quantity', $soldItem->weight);
                        $stock_item->decrement('available_length', $soldItem->quantity);
                    }
                }
            }

            if ($request->category === 'Pipe') {
                $last_balance = (new LedgerService)
                    ->getCustomerLastBalance($request->customer_id, $sell->id);

                if ($last_balance < 0) {
                    $negative_balance = $last_balance;

                    if (abs($negative_balance) > $sell->total_amount || abs($negative_balance) === $sell->total_amount) {
                        $amount = $sell->total_amount;
                    } else if (abs($negative_balance) < $sell->total_amount) {
                        $amount = abs($negative_balance);
                    }

                    Payment::query()
                        ->create([
                            'amount' => $amount,
                            'model' => Sell::class,
                            'paymentable_id' => $sell->id,
                            'payment_method' => 'Cash',
                            'payment_date' => Carbon::parse($request->date)->format('Y-m-d h:i:s'),
                            'transaction_type' => 'Debit',
                            'bank_id' => null,
                        ]);
                }
            }


            DB::commit();

            return response()->noContent();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Server Error" . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sell  $sell
     * @return \Illuminate\Http\Response
     */
    public function show(int $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_show');

        $sell = Sell::with([
            'customer',
            'sold_items.product',
            'returned_items.product',
            'stock_item',
        ])->find($sell);

        return response()->json($sell);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sell  $sell
     * @return \Illuminate\Http\Response
     */
    public function update(SellRequest $request, Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_edit');

        try {
            DB::beginTransaction();

            if ($request->category === 'Pipe') {
                // Delete old items 
                foreach ($request->old_items as $item) {
                    $soldItem = SoldItem::find($item['id']);
                    if (is_null($sell->stock_item_id)) {
                        $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
                    } else {
                        $stock_item = StockItem::query()->find($sell->stock_item_id);
                    }

                    if (!is_null($stock_item)) {
                        $stock_item->increment('available_quantity', $soldItem->weight);
                        $stock_item->increment('available_length', $soldItem->quantity);
                    }

                    $soldItem->delete();
                }

                foreach ($request->items as $item) {
                    $soldItem = $sell->sold_items()->create($item);

                    // ###
                    if ($sell->stock_item_id == $request->stock_item_id) {
                        if (is_null($sell->stock_item_id)) {
                            $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
                        } else {
                            $stock_item = StockItem::query()->find($sell->stock_item_id);
                        }

                        if (!is_null($stock_item)) {
                            $stock_item->decrement('available_quantity', $soldItem->weight);
                            $stock_item->decrement('available_length', $soldItem->quantity);
                        }
                    } else {
                        if (is_null($sell->stock_item_id) && !empty($request->stock_item_id)) {
                            $stock_item = StockItem::find($request->stock_item_id);

                            if (!is_null($stock_item)) {
                                $stock_item->decrement('available_quantity', $soldItem->weight);
                                $stock_item->decrement('available_length', $soldItem->quantity);
                            }
                        } else if (!is_null($sell->stock_item_id) && empty($request->stock_item_id)) {
                            $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();

                            if (!is_null($stock_item)) {
                                $stock_item->decrement('available_quantity', $soldItem->weight);
                                $stock_item->decrement('available_length', $soldItem->quantity);
                            }
                        }
                    }
                    // ###


                }
            }


            $sell->update($request->only([
                'date',
                'invoice_no',
                'gate_pass_id',
                'customer_id',
                'sales_tax_percentage',
                'category',
                'unit',
                'discount',
                'total_amount',
                'description',
                'stock_item_id',
            ]));

            DB::commit();

            return response()->noContent(201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["Server Error" . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a sell
     *
     * @param  App\Models\Sell $sell
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sell $sell)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_delete');

        try {
            DB::beginTransaction();

            $sell->load('sold_items');

            Payment::where('model', Sell::class)
                ->where('paymentable_id', $sell->id)
                ->delete();

            foreach ($sell->sold_items as $item) {
                $soldItem = SoldItem::find($item['id']);

                if (is_null($sell->stock_item_id)) {
                    $stock_item = StockItem::query()->where('product_id', $soldItem->product_id)->first();
                } else {
                    $stock_item = StockItem::query()->find($sell->stock_item_id);
                }

                $stock_item->increment('available_quantity', $soldItem->weight);
                $stock_item->increment('available_length', $soldItem->quantity);
            }

            $sell->delete();
            DB::commit();

            return response()->json(["success" =>  "Sell deleted successfully"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['Server Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete multiple sells.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('sell_access');
        Gate::authorize('sell_delete');

        foreach ($request->ids as $id) {
            $sell = Sell::find($id);
            $sell->delete();
            // Delete associated payments, too
            Payment::where('model', Sell::class)
                ->where('paymentable_id', $sell->id)
                ->delete();
        }

        return response()->json(["success" =>  "Sells deleted successfully"]);
    }
}
