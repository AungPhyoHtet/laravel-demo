<?php

use App\Models\Idea;
use App\Notifications\IdeaPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('links to the idea detail page in the mail', function (): void {
    $idea = Idea::factory()->create(['description' => 'Build a better mousetrap']);

    $mail = (new IdeaPublished($idea))->toMail($idea->user);

    expect($mail->viewData['url'])->toBe(route('ideas.show', $idea));

    $html = (string) $mail->render();

    expect($html)
        ->toContain(route('ideas.show', $idea))
        ->toContain('Build a better mousetrap');
});

it('stores the notification in the database', function (): void {
    $idea = Idea::factory()->create(['description' => 'Build a better mousetrap']);

    $idea->user->notify(new IdeaPublished($idea));

    $notification = $idea->user->notifications()->sole();

    expect($notification->type)->toBe(IdeaPublished::class)
        ->and($notification->data)->toBe([
            'idea_id' => $idea->id,
            'description' => 'Build a better mousetrap',
            'url' => route('ideas.show', $idea),
        ]);
});
