<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use Illuminate\Support\Facades\Event;
use Tests\FakeEvents;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use FakeEvents;

    public function test_all_badge_steps_and_events()
    {
        $user = $this->user();

        // 15 Lessons
        $this->lessons($user, 15);

        Event::assertDispatchedTimes(AchievementUnlocked::class, $achievementTimes = 3);
        Event::assertNotDispatched(BadgeUnlocked::class);

        $achievements = $user->achievements()->pluck('name');

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertNotContains('50 Lessons Watched', $achievements);
        $this->assertNotContains('First Comment Written', $achievements);

        $this->assertEquals('Beginner', $user->badge);

        // 10 Comments
        $this->comments($user, 10);

        Event::assertDispatchedTimes(AchievementUnlocked::class, $achievementTimes += 4);
        Event::assertDispatchedTimes(BadgeUnlocked::class, $badgeTimes = 1);

        $achievements = $user->achievements()->pluck('name');

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertContains('5 Comments Written', $achievements);
        $this->assertContains('10 Comments Written', $achievements);
        $this->assertNotContains('50 Lessons Watched', $achievements);

        $this->assertEquals('Intermediate', $user->badge);

        // 40 Lessons
        $this->lessons($user, 40);

        Event::assertDispatchedTimes(AchievementUnlocked::class, $achievementTimes += 2);
        Event::assertDispatchedTimes(BadgeUnlocked::class, ++$badgeTimes);

        $achievements = $user->achievements()->pluck('name');

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertContains('25 Lessons Watched', $achievements);
        $this->assertContains('50 Lessons Watched', $achievements);
        $this->assertContains('5 Comments Written', $achievements);
        $this->assertContains('10 Comments Written', $achievements);

        $this->assertEquals('Advanced', $user->badge);

        // 12 Comments
        $this->comments($user, 12);

        Event::assertDispatchedTimes(AchievementUnlocked::class, ++$achievementTimes);
        Event::assertDispatchedTimes(BadgeUnlocked::class, ++$badgeTimes);

        $achievements = $user->achievements()->pluck('name');

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertContains('25 Lessons Watched', $achievements);
        $this->assertContains('50 Lessons Watched', $achievements);
        $this->assertContains('First Comment Written', $achievements);
        $this->assertContains('5 Comments Written', $achievements);
        $this->assertContains('10 Comments Written', $achievements);
        $this->assertContains('20 Comments Written', $achievements);

        $this->assertEquals('Master', $user->badge);
    }
}
