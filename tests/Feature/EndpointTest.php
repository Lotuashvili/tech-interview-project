<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EndpointTest extends TestCase
{
    public function test_new_user()
    {
        $this->request($this->user())->assertOk()->assertExactJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [
                'First Comment Written',
                'First Lesson Watched',
            ],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }

    public function test_first_lesson()
    {
        $user = $this->user();
        $this->lessons($user);

        $this->request($user)->assertOk()->assertExactJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
            ],
            'next_available_achievements' => [
                'First Comment Written',
                '5 Lessons Watched',
            ],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 3,
        ]);
    }

    public function test_first_comment()
    {
        $user = $this->user();
        $this->comments($user);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Comment Written',
            ],
            'next_available_achievements' => [
                '3 Comments Written',
                'First Lesson Watched',
            ],
            'current_badge' => 'Beginner',
        ]);
    }

    public function test_first_lesson_and_comment()
    {
        $user = $this->user();
        $this->lessons($user);
        $this->comments($user);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                'First Comment Written',
            ],
            'next_available_achievements' => [
                '3 Comments Written',
                '5 Lessons Watched',
            ],
            'current_badge' => 'Beginner',
        ]);
    }

    public function test_intermediate_badge()
    {
        $user = $this->user();
        $this->lessons($user, 5);
        $this->comments($user, 3);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
            ],
            'next_available_achievements' => [
                '5 Comments Written',
                '10 Lessons Watched',
            ],
            'current_badge' => 'Intermediate',
        ]);
    }

    public function test_advanced_badge()
    {
        $user = $this->user();
        $this->lessons($user, 50);
        $this->comments($user, 5);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
            ],
            'next_available_achievements' => [
                '10 Comments Written',
            ],
            'current_badge' => 'Advanced',
        ]);
    }

    public function test_master_badge()
    {
        $user = $this->user();
        $this->lessons($user, 55);
        $this->comments($user, 23);

        $this->request($user)->assertOk()->assertExactJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
                '20 Comments Written',
            ],
            'next_available_achievements' => [],
            'current_badge' => 'Master',
            'next_badge' => null,
            'remaining_to_unlock_next_badge' => 0,
        ]);
    }

    public function test_all_lessons_and_no_comments()
    {
        $user = $this->user();
        $this->lessons($user, 52);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Lesson Watched',
                '5 Lessons Watched',
                '10 Lessons Watched',
                '25 Lessons Watched',
                '50 Lessons Watched',
            ],
            'next_available_achievements' => [
                'First Comment Written',
            ],
            'current_badge' => 'Intermediate',
        ]);
    }

    public function test_all_comments_and_no_lessons()
    {
        $user = $this->user();
        $this->comments($user, 23);

        $this->request($user)->assertOk()->assertJson([
            'unlocked_achievements' => [
                'First Comment Written',
                '3 Comments Written',
                '5 Comments Written',
                '10 Comments Written',
                '20 Comments Written',
            ],
            'next_available_achievements' => [
                'First Lesson Watched',
            ],
            'current_badge' => 'Intermediate',
        ]);
    }

    public function test_user_does_not_exist()
    {
        $this->get('users/99999999/achievements')->assertNotFound();
    }

    protected function request(User $user): TestResponse
    {
        return $this->get("users/$user->id/achievements");
    }
}
