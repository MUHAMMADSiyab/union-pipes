<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockSheetRequest;
use App\Models\StockSheet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('stock_sheet_access');

        $stock_sheets = StockSheet::query()
            ->withSum('entries', 'quantity')
            ->withSum('entries', 'total_weight')
            ->withSum('entries', 'total_amount')
            ->get();
        return response()->json($stock_sheets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StockSheetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockSheetRequest $request)
    {
        Gate::authorize('stock_sheet_access');
        Gate::authorize('stock_sheet_create');

        try {
            DB::beginTransaction();

            $data = ['month' => Carbon::parse($request->month . '-01')->lastOfMonth()];
            $stock_sheet = StockSheet::query()->create($data);
            foreach ($request->entries as $entry) {
                $stock_sheet->entries()->create($entry);
            }

            DB::commit();

            return response()->json([
                'message' => 'Stock Sheet Entries added successfully',
                'stock_sheet' => StockSheet::query()
                    ->with('entries')
                    ->find($stock_sheet->id),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $stock_sheet
     * @return \Illuminate\Http\Response
     */
    public function show(int $stock_sheet)
    {
        Gate::authorize('stock_sheet_show');

        $stock_sheet = StockSheet::query()
            ->with('entries')
            ->withSum('entries', 'quantity')
            ->withSum('entries', 'total_weight')
            ->withSum('entries', 'total_amount')
            ->findOrFail($stock_sheet);

        return response()->json($stock_sheet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\StockSheetRequest  $request
     * @param  App\Models\StockSheet  $stock_sheet
     * @return \Illuminate\Http\Response
     */
    public function update(StockSheetRequest $request, StockSheet $stock_sheet)
    {
        Gate::authorize('stock_sheet_access');
        Gate::authorize('stock_sheet_edit');

        try {
            DB::beginTransaction();

            $stock_sheet->load('entries');

            $data = ['month' => Carbon::parse($request->month . '-01')->lastOfMonth()];
            $stock_sheet->update($data);
            $stock_sheet->entries()->delete();

            foreach ($request->entries as $entry) {
                $stock_sheet->entries()->create($entry);
            }

            DB::commit();



            return response()->json([
                'message' => 'Stock Sheet Entries updated successfully',
                'stock_sheet' => StockSheet::query()
                    ->with('entries')
                    ->find($stock_sheet->id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\StockSheet $stock_sheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockSheet $stock_sheet)
    {
        Gate::authorize('stock_sheet_access');
        Gate::authorize('stock_sheet_delete');

        if ($stock_sheet->delete()) {
            return response()->json(['message' => 'Stock Sheet Entries deleted successfully']);
        }
    }

    public function destroy_multiple(Request $request)
    {
        Gate::authorize('stock_sheet_access');
        Gate::authorize('stock_sheet_delete');

        if (StockSheet::whereIn('id', $request->ids)->delete()) {
            return response()->json(["message" =>  "Stock Sheet Entries deleted successfully"]);
        }
    }
}
