<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::query()->create(['name' => 'Petrol']);
        Product::query()->create(['name' => 'Diesel']);
        Product::query()->create(['name' => 'Super+']);
    }
}
