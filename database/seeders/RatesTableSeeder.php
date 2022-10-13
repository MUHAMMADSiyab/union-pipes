<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class RatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::all()->each(function ($product) {
            $product->rates()->create(['rate' => 230.5, 'change_date' => date('Y-m-d')]);
        });
    }
}
