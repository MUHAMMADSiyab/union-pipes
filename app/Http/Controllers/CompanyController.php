<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    /**
     * Get all companies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('company_access');

        $companies = Company::all();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\CompanyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        Gate::authorize('company_access');
        Gate::authorize('company_create');

        $company = Company::create($request->all());

        if ($request->hasFile('logo')) {
            $company->addMediaFromRequest('logo')->toMediaCollection('companies');
        }

        return response()->json($company, 201);
    }

    /**
     * Get a single company
     *
     * @param  App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        Gate::authorize('company_access');
        Gate::authorize('company_show');

        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\CompanyRequest  $request
     * @param  App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, Company $company)
    {
        Gate::authorize('company_access');
        Gate::authorize('company_edit');

        $company->update($request->all());

        if ($request->hasFile('logo')) {
            $company->getFirstMedia('companies')->delete();
            $company->addMediaFromRequest('logo')->toMediaCollection('companies');
        }

        return response()->json(Company::find($company->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        Gate::authorize('company_access');
        Gate::authorize('company_delete');

        if ($company->delete()) {
            return response()->json(['success' => 'Company deleted successfully']);
        }
    }

    /**
     * Delete multitple companies 
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy_multiple(Request $request)
    {
        Gate::authorize('company_access');
        Gate::authorize('company_delete');

        foreach ($request->ids as $id) {
            Company::find($id)->delete();
        }

        return response()->json(['success' => 'Companies deleted successfully']);
    }
}
