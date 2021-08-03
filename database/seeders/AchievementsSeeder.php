<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class AchievementsSeeder extends Seeder
{
    protected array $achievements = [
        Lesson::class => [
            1 => 'First Lesson Watched',
            5 => '5 Lessons Watched',
            10 => '10 Lessons Watched',
            25 => '25 Lessons Watched',
            50 => '50 Lessons Watched',
        ],
        Comment::class => [
            1 => 'First Comment Written',
            3 => '3 Comments Written',
            5 => '5 Comments Written',
            10 => '10 Comments Written',
            20 => '20 Comments Written',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Achievement::count()) {
            return;
        }

        foreach ($this->achievements as $type => $achievements) {
            foreach ($achievements as $count => $name) {
                Achievement::create([
                    'name' => $name,
                    'count' => $count,
                    'achievable_type' => $type,
                ]);
            }
        }
    }
}
