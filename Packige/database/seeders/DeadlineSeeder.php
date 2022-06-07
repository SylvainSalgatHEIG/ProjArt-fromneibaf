<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeadlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deadlines')->insert([
            'name' => "Deadline 1",
            'description' => "Description deadline 1",
            'type' => "rendu",
            'course_id' => 1,
            'group_id' => 1,
            'start_date' => "2022-06-06 08:30:00",
            'end_date' => "2022-06-06 08:30:00",
        ]);

        DB::table('deadlines')->insert([
            'name' => "Deadline 1.5",
            'description' => "Description deadline 1",
            'type' => "rendu",
            'course_id' => 3,
            'group_id' => 1,
            'start_date' => "2022-06-06 10:30:00",
            'end_date' => "2022-06-06 10:30:00",
        ]);

        DB::table('deadlines')->insert([
            'name' => "Deadline 2",
            'description' => "Description deadline 2",
            'type' => "examen",
            'course_id' => 2,
            'group_id' => 1,
            'start_date' => "2022-07-06 09:00:00",
            'end_date' => "2022-07-06 11:00:00",
        ]);
    }
}
