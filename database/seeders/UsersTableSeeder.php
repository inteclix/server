<?php

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
        DB::table('users')->insert([
            'username' => "admin",
            'firstname' => "admin",
            'lastname' => "admin",
            'mail' => "admin@condor.com",
            'password' => "admin",
            'poste' => "admin",
            'tel' => "0770707070",
            'is_active' => true,
            'img1' => "",
            'img2' => "",
            'img3' => "",
        ]);
        DB::table('users')->insert([
            'username' => "COM",
            'firstname' => "Commercial",
            'lastname' => "Commercial",
            'mail' => "COM@gmail.com",
            'password' => "123456",
            'poste' => "COM",
            'tel' => "0770707070",
            'is_active' => true,
            'img1' => "",
            'img2' => "",
            'img3' => "",
        ]);
        DB::table('users')->insert([
            'username' => "GA",
            'firstname' => "Gestionnaire",
            'lastname' => "Administratif",
            'mail' => "GA@gmail.com",
            'password' => "123456",
            'poste' => "GA",
            'tel' => "0770707070",
            'is_active' => true,
            'img1' => "",
            'img2' => "",
            'img3' => "",
        ]);
        DB::table('users')->insert([
            'username' => "GM",
            'firstname' => "Gestionnaire",
            'lastname' => "Materiel",
            'mail' => "GM@condor.com",
            'password' => "123456",
            'poste' => "GM",
            'tel' => "0770707070",
            'is_active' => true,
            'img1' => "",
            'img2' => "",
            'img3' => "",
        ]);
    }
}
