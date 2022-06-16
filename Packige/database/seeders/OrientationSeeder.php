<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrientationSeeder extends Seeder
{
    /**
     * Run the database seeds for orientations.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orientations')->insert([
            'name' => "Ingénierie des Médias",
            'departement_id' => 1
        ]);
    }
}
