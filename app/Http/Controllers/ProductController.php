<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Get all companies
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('product_access');

        $companies = Product::all();
        return response()->json($companies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        Gate::authorize('product_access');
        Gate::authorize('product_create');

        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    /**
     * Get a single product
     *
     * @param  App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        Gate::authorize('product_access');
        Gate::authorize('product_show');

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @param  App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        Gate::authorize('product_access');
        Gate::authorize('product_edit');

        $product->update($request->all());

        return response()->json(Product::find($product->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        Gate::authorize('product_access');
        Gate::authorize('product_delete');

        if ($product->delete()) {
            return response()->json(['success' => 'Product deleted successfully']);
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
        Gate::authorize('product_access');
        Gate::authorize('product_delete');

        foreach ($request->ids as $id) {
            Product::find($id)->delete();
        }

        return response()->json(['success' => 'Companies deleted successfully']);
    }
}
