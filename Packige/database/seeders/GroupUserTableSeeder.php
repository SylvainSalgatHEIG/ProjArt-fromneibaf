<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds for groups and users join table.
     *
     * @return void
     */
    public function run()
    {   
        /**
         * Attach users test to IM groups
         */

        DB::table('group_user')->insert([
            'group_id' => "3",
            'user_id' => "1",
        ]);

        DB::table('group_user')->insert([
            'group_id' => "5",
            'user_id' => "1",
        ]);

        DB::table('group_user')->insert([
            'group_id' => "3",
            'user_id' => "2",
        ]);
        
        /*
        DB::table('group_user')->insert([
            'group_id' => "1",
            'user_id' => "1",
        ]);

        DB::table('group_user')->insert([
            'group_id' => "3",
            'user_id' => "1",
        ]);


        DB::table('group_user')->insert([
            'group_id' => "1",
            'user_id' => "2",
        ]);

        DB::table('group_user')->insert([
            'group_id' => "2",
            'user_id' => "3",
        ]);

        DB::table('group_user')->insert([
            'group_id' => "4",
            'user_id' => "3",
        ]);
        */
    }
}
