<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    protected string $badge_name;

    /**
     * @var \App\Models\User
     */
    protected User $user;

    /**
     * Create a new event instance.
     *
     * @param string $badge_name
     * @param \App\Models\User $user
     */
    public function __construct(string $badge_name, User $user)
    {
        $this->badge_name = $badge_name;
        $this->user = $user;
    }
}
