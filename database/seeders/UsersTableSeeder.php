<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->insert([
                [
                    'name' => 'Mr. John',
                    'email' => 'john@gmail.com',
                    'password' => bcrypt('password')
                ],

                [
                    'name' => 'Sara Smith',
                    'email' => 'sara@gmail.com',
                    'password' => bcrypt('sarasmith')
                ],
            ]);
    }
}
