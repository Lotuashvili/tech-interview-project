<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    protected string $achievement_name;

    /**
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * Create a new event instance.
     *
     * @param string $achievement_name
     * @param \App\Models\User $user
     */
    public function __construct(string $achievement_name, User $user)
    {
        $this->achievement_name = $achievement_name;
        $this->user = $user;
    }
}
