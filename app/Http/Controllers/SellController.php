<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellRequest;
use App\Models\Payment;
use App\Models\Sell;
use App\Models\SoldItem;
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

        $sells = Sell::query()
            ->with([
                'customer',
                'sold_items.product',
                'returned_items.product',
            ])
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('date', 'like', '%' . $searchTerm . '%')
                    ->orWhere('invoice_no', 'like', '%' . $searchTerm . '%')
                    ->orWhere('category', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('customer', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', '%' . $searchTerm . '%');
                    });
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'sells');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

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
                'customer_id',
                'sales_tax_percentage',
                'category',
                'unit',
                'discount',
                'total_amount',
                'description',
            ]));

            if ($request->category === 'Pipe') {
                foreach ($request->items as $item) {
                    $sell->sold_items()->create($item);
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
            'returned_items.product'
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

            $sell->update($request->only([
                'date',
                'invoice_no',
                'customer_id',
                'sales_tax_percentage',
                'category',
                'unit',
                'discount',
                'total_amount',
                'description',
            ]));

            if ($request->category === 'Pipe') {
                // Delete old items 
                foreach ($request->old_items as $item) {
                    SoldItem::find($item['id'])->delete();
                }

                foreach ($request->items as $item) {
                    $sell->sold_items()->create($item);
                }
            }

            DB::commit();

            return response()->noContent(201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Server Error", 500);
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

        if ($sell->delete()) {
            $payment = Payment::where('model', Sell::class)
                ->where('paymentable_id', $sell->id)
                ->first();

            if ($payment) {
                $payment->delete();
            }

            return response()->json(["success" =>  "Sell deleted successfully"]);
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
            $payment = Payment::where('model', Sell::class)
                ->where('paymentable_id', $sell->id)
                ->first();

            if ($payment) {
                $payment->delete();
            }
        }

        return response()->json(["success" =>  "Sells deleted successfully"]);
    }
}
