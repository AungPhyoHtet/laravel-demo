<?php

use App\Models\Idea;
use App\Models\User;
use App\Notifications\IdeaPublished;

it('shows a success toast after marking notifications as read and hides it when dismissed', function (): void {
    $user = User::factory()->create();
    $user->notify(new IdeaPublished(Idea::factory()->for($user)->create()));

    $this->actingAs($user);

    visit(route('notifications.index'))
        ->press('Mark all as read')
        ->assertVisible('.toast .alert-success')
        ->assertSeeIn('.toast', 'All notifications marked as read.')
        ->click('[aria-label="Dismiss"]')
        ->assertMissing('.toast .alert-success')
        ->assertNoJavaScriptErrors();
});
