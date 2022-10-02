<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentSetting::create([
            'payment_methods' => ['Cash', 'Cheque', 'Online Transfer'],
            'cheque_types' => ['Crossed', 'Open', 'Post-Dated',  'Stale', 'Traveller', 'Self'],
            'currency' => 'PKR'
        ]);
    }
}
