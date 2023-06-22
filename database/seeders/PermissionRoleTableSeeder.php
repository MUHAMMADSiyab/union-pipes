<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::all();

        // Admin
        foreach ($permissions as $permission) {
            DB::table('permission_role')->insert([
                'permission_id' => $permission->id,
                'role_id' => 1
            ]);
        }

        // User
        foreach ($permissions as $permission) {
            if (!in_array($permission->name, [
                'permission_access',
                'permission_create',
                'permission_edit',
                'permission_show',
                'permission_delete',
                'role_access',
                'role_create',
                'role_edit',
                'role_show',
                'role_delete',
                'user_access',
                'user_create',
                'user_edit',
                'user_show',
                'user_delete',
                'app_setting_edit',
            ])) {
                DB::table('permission_role')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => 2
                ]);
            }
        }
    }
}
