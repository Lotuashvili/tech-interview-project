<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
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
        Lesson::factory()
            ->count(20)
            ->create();

        User::factory()
            ->count(5)
            ->create();

        $this->call(AchievementsSeeder::class);
    }
}
