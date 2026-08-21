<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            TestimonialSeeder::class,
            FaqSeeder::class,
            StatisticSeeder::class,
            PageSeeder::class,
            NavigationSeeder::class,
            SettingSeeder::class,
            ContactInfoSeeder::class,
            AdminUserSeeder::class,
            LessonSeeder::class,
        ]);
    }
}
