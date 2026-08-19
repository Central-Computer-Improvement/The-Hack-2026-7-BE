<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompaniesSeeder::class,
            UserSeeder::class,
            JobsSeeder::class,
            CoursesSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}