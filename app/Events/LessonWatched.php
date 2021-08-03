<?php

namespace App\Events;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LessonWatched
{
    use Dispatchable, SerializesModels;

    /**
     * @var \App\Models\Lesson
     */
    public Lesson $lesson;

    /**
     * @var \App\Models\User
     */
    public User $user;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Lesson $lesson
     * @param \App\Models\User $user
     */
    public function __construct(Lesson $lesson, User $user)
    {
        $this->lesson = $lesson;
        $this->user = $user;
    }
}
