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
        Product::query()->create(['name' => 'Pipe PVC', 'per_kg_price' => 140, 'type' => 'Class 4']);
        Product::query()->create(['name' => 'Pipe Plastic', 'per_kg_price' => 120, 'type' => 'Class 6']);
    }
}
