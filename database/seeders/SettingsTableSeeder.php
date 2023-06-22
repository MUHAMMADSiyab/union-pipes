<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'app_name' => 'ABC Motors Ltd.',
            'phone' => '021-23923203',
            'address' => 'Building 34, Highway road.',
        ]);
    }
}
