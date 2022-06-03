<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeadlineUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deadline_user')->insert([
            'isChecked' => 0,
            'user_id' => 1,
            'deadline_id' => 1,
        ]);

        DB::table('deadline_user')->insert([
            'isChecked' => 1,
            'user_id' => 1,
            'deadline_id' => 2,
        ]);
    }
}
