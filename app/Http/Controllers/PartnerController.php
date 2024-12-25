<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PartnerController extends Controller
{

    public function all()
    {
        return response()->json(Partner::all());
    }

    /**
     * Get all partners
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('partner_access');

        $partners = Partner::all();

        return response()->json($partners);
    }

    public function search(Request $request)
    {
        Gate::authorize('partner_access');

        $partners = Partner::query()
            ->where('name', 'like', '%' . $request->searchKeyword . '%')
            ->get();

        return response()->json($partners);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PartnerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PartnerRequest $request)
    {
        Gate::authorize('partner_access');
        Gate::authorize('partner_create');

        $data =  $request->all();
        $partner = Partner::create($data);

        if ($request->hasFile('photo')) {
            $partner->addMediaFromRequest('photo')->toMediaCollection('partners');
        }

        return response()->json($partner, 201);
    }

    /**
     * Get a single partner
     *
     * @param  App\Models\Partner $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $partner)
    {
        Gate::authorize('partner_access');
        Gate::authorize('partner_show');

        return response()->json($partner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PartnerRequest  $request
     * @param  App\Models\Partner $partner
     * @return \Illuminate\Http\Response
     */
    public function update(PartnerRequest $request, Partner $partner)
    {
        Gate::authorize('partner_access');
        Gate::authorize('partner_edit');

        $data = $request->all();
        $partner->update($data);

        if ($request->hasFile('photo')) {
            if ($media = $partner->getFirstMedia('partners')) {
                $media->delete();
            }
            $partner->addMediaFromRequest('photo')->toMediaCollection('partners');
        }



        return response()->json(Partner::find($partner->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Partner $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Partner $partner)
    {
        Gate::authorize('partner_access');
        Gate::authorize('partner_delete');

        if ($partner->delete()) {
            return response()->json(['success' => 'Partner deleted successfully']);
        }
    }
}
