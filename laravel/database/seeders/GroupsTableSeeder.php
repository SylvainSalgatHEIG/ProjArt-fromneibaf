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
        $group1 = DB::table('groups')->insert([
            'name' => 'M49-1',
            'password' => Hash::make('1234')
        ]);
        $group2 = DB::table('groups')->insert([
            'name' => 'M49-2',
            'password' => Hash::make('1234')
        ]);
    }
}
