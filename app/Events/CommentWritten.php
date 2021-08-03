<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class CommentWritten
{
    use Dispatchable, SerializesModels;

    /**
     * @var \App\Models\Comment
     */
    public Comment $comment;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }
}
