<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    /**
     * Get all invoices
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('invoice_access');

        $invoices = Invoice::all();
        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\InvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        Gate::authorize('invoice_access');
        Gate::authorize('invoice_create');

        $invoice = Invoice::create($request->all());

        return response()->json($invoice, 201);
    }

    /**
     * Get a single invoice
     *
     * @param  int $invoice (id)
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        Gate::authorize('invoice_access');
        Gate::authorize('invoice_show');

        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\InvoiceRequest  $request
     * @param  App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        Gate::authorize('invoice_access');
        Gate::authorize('invoice_edit');

        $invoice->update($request->all());

        return response()->json($invoice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Invoice $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        Gate::authorize('invoice_access');
        Gate::authorize('invoice_delete');

        if ($invoice->delete()) {
            return response()->json(['success' => 'Invoice deleted successfully']);
        }
    }

    /**
     * Delete multitple invoices 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('invoice_access');
        Gate::authorize('invoice_delete');

        foreach ($request->ids as $id) {
            Invoice::find($id)->delete();
        }

        return response()->json(['success' => 'Companies deleted successfully']);
    }
}
