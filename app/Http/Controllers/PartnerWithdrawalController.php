<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerWithdrawalRequest;
use App\Http\Resources\PartnerWithdrawalResource;
use App\Models\PartnerWithdrawal;
use App\Models\Payment;
use App\Services\OrderByService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PartnerWithdrawalController extends Controller
{
    /**
     * Get all partner withdrawals
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('partner_withdrawal_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $partner_withdrawals = PartnerWithdrawal::with(['payment', 'partner'])
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'partner_withdrawals');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

        return response()->json($partner_withdrawals);
    }

    public function search_partner_withdrawals()
    {
        Gate::authorize('partner_withdrawal_access');

        $orderBy = request('orderBy');
        $orderDirection = request()->boolean('orderByDesc') ? 'desc' : 'asc';

        $partner_withdrawals = PartnerWithdrawal::query()
            ->with(['partner'])
            ->where(function ($query) {
                $searchTerm = request('search');
                $query->where('title', 'like', '%' . $searchTerm . '%');
            })
            ->when(Str::contains($orderBy, '.'), function ($query) use ($orderBy, $orderDirection) {
                (new OrderByService)->applyRelationshipOrderBy($query, $orderBy, $orderDirection, 'partner_withdrawals');
            }, function ($query) use ($orderBy, $orderDirection) {
                $query->orderBy($orderBy, $orderDirection);
            })
            ->paginate(request('itemsPerPage'));

        return response()->json($partner_withdrawals);
    }

    /**
     * Add a new partner withdrawal
     *
     * @param  \App\Http\Requests\PartnerWithdrawalRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartnerWithdrawalRequest $request)
    {
        Gate::authorize('partner_withdrawal_access');
        Gate::authorize('partner_withdrawal_create');

        $partner_withdrawal = PartnerWithdrawal::create($request->all());

        return response()->json($partner_withdrawal, 201);
    }

    /**
     * Get a single partner withdrawal
     * @param App\PartnerWithdrawal
     * @return \Illuminate\Http\Response
     */
    public function show(PartnerWithdrawal $partner_withdrawal)
    {
        Gate::authorize('partner_withdrawal_access');
        Gate::authorize('partner_withdrawal_show');

        return response()->json($partner_withdrawal);
    }


    /**
     * Update a partner withdrawal
     *
     * @param  \App\Http\Requests\PartnerWithdrawalRequest  $request
     * @param  App\Models\PartnerWithdrawal  $partner_withdrawal
     * @return \Illuminate\Http\Response
     */
    public function update(PartnerWithdrawalRequest $request, PartnerWithdrawal $partner_withdrawal)
    {
        Gate::authorize('partner_withdrawal_access');
        Gate::authorize('partner_withdrawal_edit');

        $old_partner_withdrawal = $partner_withdrawal->getOriginal();

        $updatedPartnerWithdrawal = tap($partner_withdrawal)->update($request->all());

        return response()->json([
            'updated_partner_withdrawal' =>  $updatedPartnerWithdrawal,
            'old_partner_withdrawal' =>  $old_partner_withdrawal
        ]);
    }

    /**
     * Delete a partner withdrawal
     *
     * @param  App\Models\PartnerWithdrawal $partner_withdrawal
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartnerWithdrawal $partner_withdrawal)
    {
        Gate::authorize('partner_withdrawal_access');
        Gate::authorize('partner_withdrawal_delete');

        if ($partner_withdrawal->delete()) {
            Payment::where('model', PartnerWithdrawal::class)
                ->where('paymentable_id', $partner_withdrawal->id)
                ->first()
                ->delete(); // delete payment
            return response()->json(["success" =>  "Partner Withdrawal deleted successfully"]);
        }
    }

    /**
     * Delete multiple partner withdrawals.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('partner_withdrawal_access');
        Gate::authorize('partner_withdrawal_delete');

        foreach ($request->ids as $id) {
            $partner_withdrawal = PartnerWithdrawal::find($id);
            $partner_withdrawal->delete();
            Payment::where('model', PartnerWithdrawal::class)
                ->where('paymentable_id', $partner_withdrawal->id)
                ->first()
                ->delete(); // delete payment
        }

        return response()->json(["success" =>  "Partner Withdrawals deleted successfully"]);
    }
}
