<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;

class CheckForAchievements
{
    /**
     * List of countable relationships based on event name
     *
     * @var array|string[]
     */
    protected array $relations = [
        LessonWatched::class => [
            'key' => 'lesson',
            'relation' => 'watched',
        ],
        CommentWritten::class => [
            'key' => 'comment',
            'relation' => 'comments',
        ],
    ];

    /**
     * Handle the event.
     *
     * @param $event
     *
     * @return void
     */
    public function handle($event)
    {
        $type = get_class($event);

        if (!array_key_exists($type, $this->relations)) {
            return;
        }

        /** @var \App\Models\User $user */
        $user = is_a($event, CommentWritten::class) ? $event->comment->user : $event->user;
        $relation = $this->relations[$type]['relation'];
        $key = $this->relations[$type]['key'];
        $model = get_class($event->$key);
        $badgeBefore = $user->badge;

        $count = $user->$relation()->count();

        // Fetching all locked achievements, can possibly unlock more than one at once
        $achievements = Achievement::where('achievable_type', $model)
            ->where('count', '<=', $count)
            ->whereDoesntHave('users', fn($query) => $query->where('id', $user->id))
            ->oldest('count') // Sort by ascending order to fire events chronologically
            ->get();

        if ($achievements->isEmpty()) {
            return;
        }

        $user->achievements()->syncWithoutDetaching($achievements);

        $achievements->each(fn(Achievement $achievement) => event(new AchievementUnlocked($achievement->name, $user)));

        $badgeAfter = $user->badge;

        if ($badgeBefore !== $badgeAfter) {
            event(new BadgeUnlocked($badgeAfter, $user));
        }
    }
}
