<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ['name' => 'user_management_access'],

            ['name' => 'permission_access'],
            ['name' => 'permission_create'],
            ['name' => 'permission_edit'],
            ['name' => 'permission_show'],
            ['name' => 'permission_delete'],

            ['name' => 'role_access'],
            ['name' => 'role_create'],
            ['name' => 'role_edit'],
            ['name' => 'role_show'],
            ['name' => 'role_delete'],

            ['name' => 'user_access'],
            ['name' => 'user_create'],
            ['name' => 'user_edit'],
            ['name' => 'user_show'],
            ['name' => 'user_delete'],

            ['name' => 'product_access'],
            ['name' => 'product_create'],
            ['name' => 'product_edit'],
            ['name' => 'product_show'],
            ['name' => 'product_delete'],

            ['name' => 'tank_access'],
            ['name' => 'tank_create'],
            ['name' => 'tank_edit'],
            ['name' => 'tank_show'],
            ['name' => 'tank_delete'],

            ['name' => 'dispenser_access'],
            ['name' => 'dispenser_create'],
            ['name' => 'dispenser_edit'],
            ['name' => 'dispenser_show'],
            ['name' => 'dispenser_delete'],

            ['name' => 'nozzle_access'],
            ['name' => 'nozzle_create'],
            ['name' => 'nozzle_edit'],
            ['name' => 'nozzle_show'],
            ['name' => 'nozzle_delete'],
            
            ['name' => 'rate_access'],
            ['name' => 'rate_edit'],

            ['name' => 'app_setting_edit'],

        ];


        collect($permissions)->each(function ($permission) {
            Permission::create($permission);
        });
    }
}
