<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkPaymentRequest;
use App\Models\BulkPayment;
use App\Models\Payment;
use App\Services\BulkPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BulkPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BulkPaymentService $bulkPaymentService)
    {
        $bulk_payments = $bulkPaymentService->getBulkPayments();
        return response()->json($bulk_payments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BulkPaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BulkPaymentRequest $request, BulkPaymentService $bulkPaymentService)
    {
        Gate::authorize('bulk_payment_create');

        return $bulkPaymentService->processBulkPayment($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BulkPayment $bulk_payment)
    {
        Gate::authorize('bulk_payment_show');

        $bulk_payment->load('customer');
        $bulk_payment->load('bank');

        return response()->json($bulk_payment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BulkPayment $bulk_payment)
    {
        Gate::authorize('bulk_payment_delete');

        try {
            DB::beginTransaction();

            Payment::query()
                ->where('bulk_payment_id', $bulk_payment->id)
                ->delete();

            $bulk_payment->delete();

            DB::commit();

            return response()->json(["success" =>  "Bulk payment deleted successfully"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
