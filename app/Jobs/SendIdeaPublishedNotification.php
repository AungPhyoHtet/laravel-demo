<?php

namespace App\Jobs;

use App\Models\Idea;
use App\Notifications\IdeaPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendIdeaPublishedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 10;

    /**
     * Delete the job if the idea no longer exists.
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public Idea $idea) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->idea->user->notify(new IdeaPublished($this->idea));
    }
}
