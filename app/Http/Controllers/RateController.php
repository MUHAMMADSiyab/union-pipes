<?php

namespace App\Http\Controllers;

use App\Http\Requests\RatesRequest;
use App\Models\Product;
use App\Models\Rate;
use Illuminate\Support\Facades\Gate;

class RateController extends Controller
{
    public function index()
    {
        Gate::authorize('rate_access');

        $rates = Rate::query()
            ->with('product')
            ->orderBy('change_date', 'desc')
            ->get();

        return response()->json($rates);
    }

    public function get_current_rates()
    {
        foreach (Product::all() as $product) {
            $current_rates[] = Rate::query()
                ->orderBy('change_date', 'desc')
                ->with('product')
                ->where('product_id', $product->id)
                ->first();
        }

        return response()->json($current_rates);
    }

    public function update(RatesRequest $request)
    {
        Gate::authorize('rate_access');
        Gate::authorize('rate_edit');

        $records = [];
        
        foreach ($request->rates as $rate) {
            $record_exists = Rate::query()
                ->where('product_id', $rate['product_id'])
                ->where('change_date', $request->change_date)
                ->where('rate', $rate['rate'])
                ->exists();

            if (!$record_exists) {
                $new_rate = Rate::create([
                    'product_id' => $rate['product_id'],
                    'change_date' => $request->change_date,
                    'rate' => $rate['rate'],
                ]);

                $records[] = Rate::with('product')->find($new_rate->id);
            }
        }

        return response()->json($records);
    }
}
