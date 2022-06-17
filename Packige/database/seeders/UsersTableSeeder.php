<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Group;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds for users.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        /**
         * 2 users for application testing
         */
        DB::table('users')->insert([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@heig-vd.ch',
            'password' => Hash::make('password')
        ]);
        
        DB::table('users')->insert([
            'firstname' => 'Evan',
            'lastname' => 'You',
            'email' => 'evan.you@heig-vd.ch',
            'password' => Hash::make('password')
        ]);

        /*
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'firstname' => 'Nom' . $i,
                'lastname' => 'Prénom' . $i,
                'email' => 'email' . $i . '@gmx.ch',
                'password' => Hash::make('password' . $i)
            ]);
        }
        */

    }
}
