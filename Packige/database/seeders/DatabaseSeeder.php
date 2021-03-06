<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DepartmentSeeder::class);
        $this->call(OrientationSeeder::class);
        $this->call(PromotionSeeder::class);
        $this->call(GroupsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GroupUserTableSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(DeadlineSeeder::class);
        $this->call(DeadlineUserSeeder::class);
        $this->call(LinksTableSeeder::class); 
    }
}
