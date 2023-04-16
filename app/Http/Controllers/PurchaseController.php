<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchasedItem;
use App\Services\LedgerService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('purchase_access');

        $purchases = Purchase::query()
            ->with([
                'company',
                'purchased_items',
                'purchased_items.purchase_item',
            ])
            ->get();

        return response()->json($purchases);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseRequest $request)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_create');

        try {
            DB::beginTransaction();

            $purchase = Purchase::create($request->only([
                'date',
                'invoice_no',
                'company_id',
                'sales_tax_percentage',
                'total_amount',
                'category',
            ]));

            if ($request->category === 'Raw Material' || $request->category === 'Other') {
                foreach ($request->items as $item) {
                    $purchase->purchased_items()->create($item);
                }
            }


            if ($request->category === 'Raw Material' || $request->category === 'Other') {
                $last_balance = (new LedgerService)
                    ->getCompanyLastBalance($request->company_id, $purchase->id);

                if ($last_balance < 0) {
                    $negative_balance = $last_balance;

                    if (abs($negative_balance) > $purchase->total_amount || abs($negative_balance) === $purchase->total_amount) {
                        $amount = $purchase->total_amount;
                    } else if (abs($negative_balance) < $purchase->total_amount) {
                        $amount = abs($negative_balance);
                    }

                    Payment::query()
                        ->create([
                            'amount' => $amount,
                            'model' => Purchase::class,
                            'paymentable_id' => $purchase->id,
                            'payment_method' => 'Cash',
                            'payment_date' => Carbon::parse($request->date)->format('Y-m-d h:i:s'),
                            'transaction_type' => 'Credit',
                            'bank_id' => null,
                        ]);
                }
            }

            DB::commit();

            return response()->noContent();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Server Error", 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(int $purchase)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_show');

        $purchase = Purchase::with([
            'company',
            'purchased_items',
            'purchased_items.purchase_item'
        ])->find($purchase);

        return response()->json($purchase);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_edit');

        try {
            DB::beginTransaction();

            $purchase->update($request->only([
                'date',
                'invoice_no',
                'company_id',
                'sales_tax_percentage',
                'total_amount',
                'category',
            ]));

            if ($request->category === 'Raw Material' || $request->category === 'Other') {
                // Delete old items 
                foreach ($request->old_items as $item) {
                    PurchasedItem::find($item['id'])->delete();
                }

                foreach ($request->items as $item) {
                    $purchase->purchased_items()->create($item);
                }
            }

            DB::commit();

            return response()->noContent(201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json("Server Error" . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a purchase
     *
     * @param  App\Models\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_delete');

        if ($purchase->delete()) {
            $payment = Payment::where('model', Purchase::class)
                ->where('paymentable_id', $purchase->id)
                ->first();

            if ($payment) {
                $payment->delete();
            }

            return response()->json(["success" =>  "Purchase deleted successfully"]);
        }
    }

    /**
     * Delete multiple purchases.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('purchase_access');
        Gate::authorize('purchase_delete');

        foreach ($request->ids as $id) {
            $purchase = Purchase::find($id);
            $purchase->delete();
            $payment = Payment::where('model', Purchase::class)
                ->where('paymentable_id', $purchase->id)
                ->first();

            if ($payment) {
                $payment->delete();
            }
        }

        return response()->json(["success" =>  "Purchases deleted successfully"]);
    }
}
