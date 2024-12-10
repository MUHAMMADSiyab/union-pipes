<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\StockItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('product_access');

        $products = Product::all();
        return response()->json($products);
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

        try {
            DB::beginTransaction();
            $product = Product::create($request->all());
            StockItem::query()->create([
                'product_id' => $product->id,
                'name' => $product->product_full_name,
                'available_quantity' => 0,
                'available_length' => 0,
            ]);
            DB::commit();
            return response()->json($product, 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Unable to add product'], 500);
        }
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

        try {
            DB::beginTransaction();

            $product->update($request->all());
            $updatedProduct = Product::find($product->id);
            StockItem::query()
                ->where('product_id', $product->id)
                ->update([
                    'name' => $product->product_full_name,
                ]);

            DB::commit();
            return response()->json($updatedProduct);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Unable to update product'], 500);
        }
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
     * Delete multitple products 
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

        return response()->json(['success' => 'Products deleted successfully']);
    }
}
