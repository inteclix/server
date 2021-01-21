<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name' => "NAUVEAU_VEHICULE",
        ]);
        DB::table('roles')->insert([
            'name' => "LISTE_VEHICULES",
        ]);
        DB::table('roles')->insert([
            'name' => "VEHICULES_GROUPES",
        ]);
        DB::table('roles')->insert([
            'name' => "STATUS_VEHICULES",
        ]);
        DB::table('roles')->insert([
            'name' => "CHANGER_STATUT_VEHICULE",
        ]);
        DB::table('roles')->insert([
            'name' => "NAUVEAU_CONDUCTEUR",
        ]);
        DB::table('roles')->insert([
            'name' => "LISTE_CONDUCTEURS",
        ]);
        DB::table('roles')->insert([
            'name' => "NAUVEAU_CLIENT",
        ]);
        DB::table('roles')->insert([
            'name' => "LISTE_CLIENTS",
        ]);
        DB::table('roles')->insert([
            'name' => "NAUVEAU_DECHARGE",
        ]);
        DB::table('roles')->insert([
            'name' => "NOUVELLE_CHECKLIST",
        ]);
        DB::table('roles')->insert([
            'name' => "LISTE_DECHARGES",
        ]);
        DB::table('roles')->insert([
            'name' => "DECHARGES_RESTITUER",
        ]);
        DB::table('roles')->insert([
            'name' => "LISTE_MISSIONS_MARCHANDISES",
        ]);
        DB::table('roles')->insert([
            'name' => "NOUVELLE_MISSION_MARCHANDISES",
        ]);
    }
}
