<?php

namespace Tests;

use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker, DatabaseTransactions;

    /**
     * Override to add FakeEvents support
     *
     * @return array
     */
    public function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[FakeEvents::class])) {
            $this->fakeEvents();
        }

        return $uses;
    }

    protected function user(): User
    {
        return User::factory()->create();
    }

    protected function lessons(User $user, int $count = 1, bool $watched = true, bool $fireEvent = true): Collection
    {
        $user->lessons()->syncWithPivotValues(
            $lessons = Lesson::factory()->count($count)->create(),
            compact('watched'),
            false
        );

        if ($fireEvent) {
            event(new LessonWatched($lessons->last(), $user));
        }

        return $lessons;
    }

    protected function comments(User $user, int $count = 1, bool $fireEvent = true): Collection
    {
        $comments = Comment::factory()->count($count)->create([
            'user_id' => $user->id,
        ]);

        if ($fireEvent) {
            event(new CommentWritten($comments->last()));
        }

        return $comments;
    }
}
