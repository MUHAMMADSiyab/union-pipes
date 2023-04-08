<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

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

            ['name' => 'meter_access'],
            ['name' => 'meter_create'],
            ['name' => 'meter_edit'],
            ['name' => 'meter_show'],
            ['name' => 'meter_delete'],

            ['name' => 'rate_access'],
            ['name' => 'rate_edit'],

            ['name' => 'bank_access'],
            ['name' => 'bank_create'],
            ['name' => 'bank_edit'],
            ['name' => 'bank_show'],
            ['name' => 'bank_delete'],

            ['name' => 'vehicle_access'],
            ['name' => 'vehicle_create'],
            ['name' => 'vehicle_edit'],
            ['name' => 'vehicle_show'],
            ['name' => 'vehicle_delete'],

            ['name' => 'transaction_access'],
            ['name' => 'transaction_create'],
            ['name' => 'transaction_edit'],
            ['name' => 'transaction_show'],
            ['name' => 'transaction_delete'],

            ['name' => 'expense_access'],
            ['name' => 'expense_create'],
            ['name' => 'expense_edit'],
            ['name' => 'expense_show'],
            ['name' => 'expense_delete'],

            ['name' => 'employee_access'],
            ['name' => 'employee_create'],
            ['name' => 'employee_edit'],
            ['name' => 'employee_show'],
            ['name' => 'employee_delete'],

            ['name' => 'salary_access'],
            ['name' => 'salary_create'],
            ['name' => 'salary_edit'],
            ['name' => 'salary_show'],
            ['name' => 'salary_delete'],

            ['name' => 'company_access'],
            ['name' => 'company_create'],
            ['name' => 'company_edit'],
            ['name' => 'company_show'],
            ['name' => 'company_delete'],

            ['name' => 'purchase_item_access'],
            ['name' => 'purchase_item_create'],
            ['name' => 'purchase_item_edit'],
            ['name' => 'purchase_item_show'],
            ['name' => 'purchase_item_delete'],

            ['name' => 'purchase_access'],
            ['name' => 'purchase_create'],
            ['name' => 'purchase_edit'],
            ['name' => 'purchase_show'],
            ['name' => 'purchase_delete'],

            ['name' => 'purchase_access'],
            ['name' => 'purchase_create'],
            ['name' => 'purchase_edit'],
            ['name' => 'purchase_show'],
            ['name' => 'purchase_delete'],

            ['name' => 'sell_access'],
            ['name' => 'sell_create'],
            ['name' => 'sell_edit'],
            ['name' => 'sell_show'],
            ['name' => 'sell_delete'],

            ['name' => 'customer_access'],
            ['name' => 'customer_create'],
            ['name' => 'customer_edit'],
            ['name' => 'customer_show'],
            ['name' => 'customer_delete'],

            ['name' => 'account_access'],
            ['name' => 'account_create'],
            ['name' => 'account_edit'],
            ['name' => 'account_show'],
            ['name' => 'account_delete'],

            ['name' => 'invoice_access'],
            ['name' => 'invoice_create'],
            ['name' => 'invoice_edit'],
            ['name' => 'invoice_show'],
            ['name' => 'invoice_delete'],

            ['name' => 'vehicle_transaction_access'],
            ['name' => 'vehicle_transaction_create'],
            ['name' => 'vehicle_transaction_edit'],
            ['name' => 'vehicle_transaction_show'],
            ['name' => 'vehicle_transaction_delete'],

            ['name' => 'payment_access'],
            ['name' => 'payment_create'],
            ['name' => 'payment_edit'],
            ['name' => 'payment_show'],
            ['name' => 'payment_delete'],

            ['name' => 'gate_pass_access'],
            ['name' => 'gate_pass_create'],
            ['name' => 'gate_pass_edit'],
            ['name' => 'gate_pass_show'],
            ['name' => 'gate_pass_delete'],

            ['name' => 'report_access'],

            ['name' => 'billing_access'],

            ['name' => 'payment_setting_edit'],

            ['name' => 'app_setting_edit'],

        ];


        collect($permissions)->each(function ($permission) {
            Permission::create($permission);
        });
    }
}
