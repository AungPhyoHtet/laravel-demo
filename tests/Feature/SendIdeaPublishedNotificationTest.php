<?php

use App\Jobs\SendIdeaPublishedNotification;
use App\Models\Idea;
use App\Notifications\IdeaPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('notifies the idea owner', function (): void {
    Notification::fake();
    $idea = Idea::factory()->create();

    (new SendIdeaPublishedNotification($idea))->handle();

    Notification::assertSentTo($idea->user, IdeaPublished::class, fn (IdeaPublished $notification) => $notification->idea->is($idea));
});
