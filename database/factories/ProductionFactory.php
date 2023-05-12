<?php

namespace Database\Factories;

use App\Models\Production;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionFactory extends Factory
{
    protected $model = Production::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create();

        return [
            'machine_id' => $faker->numberBetween(1, 3),
            'employee_id' => $faker->randomNumber(),
            'shift' => $faker->randomElement(['A', 'B', 'C']),
            'date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'product_id' => $faker->numberBetween(1, 2),
            'weight' => $faker->randomFloat(2, 1, 100),
            'quantity' => $faker->randomFloat(2, 1, 100),
            'total_weight' => $faker->randomFloat(2, 100, 1000),
            'description' => $faker->text(),
        ];
    }
}
