<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Company;
use App\Models\Customer;

class UpdateCompanyAndCustomerDataSeeder extends Seeder
{
	protected $faker;

	public function __construct(Faker $faker)
	{
		$this->faker = $faker;
	}

	public function run()
	{
		// Update Company names
		Company::chunk(1000, function ($companies) {
			foreach ($companies as $company) {
				$company->name = $this->faker->company;
				$company->save();
			}
		});

		// Randomize Customer data
		Customer::chunk(1000, function ($customers) {
			foreach ($customers as $customer) {
				$customer->name = $this->faker->name;
				$customer->cnic = $this->faker->unique()->numerify('###########');
				$customer->phone = $this->faker->unique()->phoneNumber;
				$customer->address = $this->faker->address;
				$customer->save();
			}
		});
	}
}
