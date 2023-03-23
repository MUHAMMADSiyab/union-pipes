<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchasedItem;

class ReportService
{
    public function getPurchaseReport($request)
    {
        $purchases = Purchase::with('purchased_items', 'company', 'purchased_items.purchase_item')
            ->whereBetween('date', [$request->from_date, $request->to_date])
            ->whereIn('company_id', $request->companies)
            ->get()
            ->groupBy('company_id')
            ->map(function ($company_purchases, $company_id) {
                $purchased_items = $company_purchases->flatMap(function ($purchase) {
                    return $purchase->purchased_items;
                })->groupBy('purchase_item_id')->map(function ($items, $purchase_item_id) {
                    $grand_total = $items->sum('grand_total');
                    $purchase_item_name = $items->first()->purchase_item->name;
                    return [
                        'purchase_item_id' => $purchase_item_id,
                        'purchase_item_name' => $purchase_item_name,
                        'grand_total' => $grand_total
                    ];
                })->values();
                $company_name = $company_purchases->first()->company->name;
                $paid = $company_purchases->sum('paid');
                $balance = $company_purchases->sum('balance');
                return [
                    'company_id' => $company_id,
                    'company_name' => $company_name,
                    'paid' => $paid,
                    'balance' => $balance,
                    'overall_grand_total' => $purchased_items->sum('grand_total'),
                    'purchased_items' => $purchased_items
                ];
            })->values();

        return $purchases;
    }

    public function getPurchasedItemsReport($request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $companyIds = $request->companies;

        $purchasedItems = PurchasedItem::with(['purchase' => function ($query) use ($fromDate, $toDate, $companyIds) {
            $query->whereBetween('date', [$fromDate, $toDate])
                ->whereIn('company_id', $companyIds)
                ->with('company');
        }])->get();

        $data = $purchasedItems->groupBy('purchase.company.id')->map(function ($items, $companyId) {
            $companyName = $items->first()->purchase->company->name;

            $totalQuantity = $items->sum('quantity');
            $totalTotal = $items->sum('total');
            $totalSalesTax = $items->sum('sales_tax');
            $totalGrandTotal = $items->sum('grand_total');

            $purchasedItemData = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->purchase_item->name,
                    'quantity' => $item->quantity,
                    'rate' => $item->rate,
                    'total' => $item->total,
                    'sales_tax' => $item->sales_tax,
                    'grand_total' => $item->grand_total,
                    'invoice_no' => $item->purchase->invoice_no,
                    'date' => $item->purchase->date,
                ];
            });

            return [
                'company_id' => $companyId,
                'company_name' => $companyName,
                'total_total' => $totalTotal,
                'total_sales_tax' => $totalSalesTax,
                'total_quantity' => $totalQuantity,
                'total_grand_total' => $totalGrandTotal,
                'purchased_items' => $purchasedItemData,
            ];
        })->values()->all();

        $totals['overallQuantity'] = $purchasedItems->sum('quantity');
        $totals['overallTotal'] = $purchasedItems->sum('total');
        $totals['overallSalexTax'] = $purchasedItems->sum('sales_tax');
        $totals['overallGrandTotal'] = $purchasedItems->sum('grand_total');

        return compact('data', 'totals');
    }
}
