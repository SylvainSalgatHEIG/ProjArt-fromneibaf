<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->delete();
        DB::table('groups')->insert([
            'name' => '1',
            'password' => Hash::make('123456'),
            'promotion_id' => '1'
        ]);

        DB::table('groups')->insert([
            'name' => '1',
            'password' => Hash::make('123456'),
            'promotion_id' => '2'
        ]);

        DB::table('groups')->insert([
            'name' => '2',
            'password' => Hash::make('123456'),
            'promotion_id' => '2'
        ]);

        DB::table('groups')->insert([
            'name' => '1',
            'password' => Hash::make('123456'),
            'promotion_id' => '3'
        ]);

        DB::table('groups')->insert([
            'name' => '2',
            'password' => Hash::make('123456'),
            'promotion_id' => '3'
        ]);

        DB::table('groups')->insert([
            'name' => '3',
            'password' => Hash::make('123456'),
            'promotion_id' => '3'
        ]);
    }
}
