<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 2,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 3,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 4,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 5,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 6,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 7,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 8,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 9,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 10,
        ]);

        // COM

        DB::table('role_user')->insert([
            'user_id' => 2,
            'role_id' => 5,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 2,
            'role_id' => 6,
        ]);

        // GA

        DB::table('role_user')->insert([
            'user_id' => 3,
            'role_id' => 3,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 3,
            'role_id' => 4,
        ]);
        // GM

        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 10,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 7,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 8,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 9,
        ]);
        DB::table('role_user')->insert([
            'user_id' => 4,
            'role_id' => 10,
        ]);
    }
}
