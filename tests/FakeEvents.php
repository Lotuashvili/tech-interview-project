<?php

namespace Tests;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\Event;

trait FakeEvents
{
    public function fakeEvents()
    {
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class,
        ]);
    }
}
