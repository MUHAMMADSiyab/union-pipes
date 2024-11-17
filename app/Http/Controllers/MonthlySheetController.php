<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlySheetRequest;
use App\Models\MonthlySheet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MonthlySheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('monthly_sheet_access');

        $monthly_sheets = MonthlySheet::query()->get();
        return response()->json($monthly_sheets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\MonthlySheetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MonthlySheetRequest $request)
    {
        Gate::authorize('monthly_sheet_access');
        Gate::authorize('monthly_sheet_create');

        try {
            DB::beginTransaction();

            $data = [
                'month' => Carbon::parse($request->month . '-01')->lastOfMonth(),
                'previous_month_total' => $request->previous_month_total
            ];
            $monthly_sheet = MonthlySheet::query()->create($data);

            foreach ($request->entries as $entry) {
                $monthly_sheet->entries()->create($entry);
            }

            DB::commit();

            return response()->json([
                'message' => 'Monthly Sheet Entries added successfully',
                'monthly_sheet' => MonthlySheet::query()
                    ->with('entries')
                    ->find($monthly_sheet->id),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $monthly_sheet
     * @return \Illuminate\Http\Response
     */
    public function show(int $monthly_sheet)
    {
        Gate::authorize('monthly_sheet_show');

        $monthly_sheet = MonthlySheet::query()
            ->with('entries')
            ->findOrFail($monthly_sheet);

        return response()->json($monthly_sheet);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\MonthlySheetRequest  $request
     * @param  App\Models\MonthlySheet  $monthly_sheet
     * @return \Illuminate\Http\Response
     */
    public function update(MonthlySheetRequest $request, MonthlySheet $monthly_sheet)
    {
        Gate::authorize('monthly_sheet_access');
        Gate::authorize('monthly_sheet_edit');

        try {
            DB::beginTransaction();

            $monthly_sheet->load('entries');

            $data = [
                'previous_month_total' => $request->previous_month_total,
                'month' => Carbon::parse($request->month . '-01')->lastOfMonth()
            ];
            $monthly_sheet->update($data);
            $monthly_sheet->entries()->delete();

            foreach ($request->entries as $entry) {
                $monthly_sheet->entries()->create($entry);
            }

            DB::commit();


            return response()->json([
                'message' => 'Monthly Sheet Entries updated successfully',
                'monthly_sheet' => MonthlySheet::query()
                    ->with('entries')
                    ->find($monthly_sheet->id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\MonthlySheet $monthly_sheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(MonthlySheet $monthly_sheet)
    {
        Gate::authorize('monthly_sheet_access');
        Gate::authorize('monthly_sheet_delete');

        if ($monthly_sheet->delete()) {
            return response()->json(['message' => 'Monthly Sheet Entries deleted successfully']);
        }
    }

    public function destroy_multiple(Request $request)
    {
        Gate::authorize('monthly_sheet_access');
        Gate::authorize('monthly_sheet_delete');

        if (MonthlySheet::whereIn('id', $request->ids)->delete()) {
            return response()->json(["message" =>  "Monthly Sheet Entries deleted successfully"]);
        }
    }
}
