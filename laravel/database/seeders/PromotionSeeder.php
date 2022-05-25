<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promotions')->insert([
            'name' => "M48",
            'start_year' => "2019-09-01",
            'end_year' => "2022-07-01",
            'orientation_id' => 1
        ]);

        DB::table('promotions')->insert([
            'name' => "M49",
            'start_year' => "2020-09-01",
            'end_year' => "2023-07-01",
            'orientation_id' => 1
        ]);

        DB::table('promotions')->insert([
            'name' => "M50",
            'start_year' => "2021-09-01",
            'end_year' => "2024-07-01",
            'orientation_id' => 1
        ]);
        
    }
}
