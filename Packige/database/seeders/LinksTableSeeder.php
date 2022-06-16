<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds for personals user's links.
     *
     * @return void
     */
    public function run()
    {
        DB::table('links')->insert([
            'name' => "Gaps Chabloz",
            'link' => "https://chabloz.eu/horaires/",
            'user_id' => "1",
        ]);

        DB::table('links')->insert([
            'name' => "Bruno Simon",
            'link' => "https://bruno-simon.com/",
            'user_id' => "1",
        ]);
    }
}
