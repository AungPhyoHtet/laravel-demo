<?php

use App\Models\Idea;
use App\Models\User;
use App\Notifications\IdeaPublished;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

/**
 * Store an IdeaPublished database notification for the given idea's owner.
 */
function storeIdeaPublishedNotification(Idea $idea): DatabaseNotification
{
    return $idea->user->notifications()->create([
        'id' => Str::uuid()->toString(),
        'type' => IdeaPublished::class,
        'data' => (new IdeaPublished($idea))->toArray($idea->user),
    ]);
}

it('redirects guests to the login page', function (): void {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

it('lists the user\'s notifications', function (): void {
    $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);
    storeIdeaPublishedNotification($idea);
    $otherIdea = Idea::factory()->create(['description' => 'Someone else entirely']);
    storeIdeaPublishedNotification($otherIdea);

    $response = $this->actingAs($this->user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertSee('Build a better mousetrap');
    $response->assertDontSee('Someone else entirely');
});

it('shows the unread count in the nav', function (): void {
    storeIdeaPublishedNotification(Idea::factory()->for($this->user)->create());
    storeIdeaPublishedNotification(Idea::factory()->for($this->user)->create());

    $response = $this->actingAs($this->user)->get(route('ideas.index'));

    $response->assertSee('Notifications (2 unread)');
});

it('marks the notification as read and redirects to the idea', function (): void {
    $idea = Idea::factory()->for($this->user)->create();
    $notification = storeIdeaPublishedNotification($idea);

    $response = $this->actingAs($this->user)->get(route('notifications.show', $notification->id));

    $response->assertRedirect(route('ideas.show', $idea));
    expect($notification->fresh()->read())->toBeTrue();
});

it('returns not found for another user\'s notification', function (): void {
    $notification = storeIdeaPublishedNotification(Idea::factory()->create());

    $response = $this->actingAs($this->user)->get(route('notifications.show', $notification->id));

    $response->assertNotFound();
    expect($notification->fresh()->read())->toBeFalse();
});

it('marks all notifications as read', function (): void {
    $first = storeIdeaPublishedNotification(Idea::factory()->for($this->user)->create());
    $second = storeIdeaPublishedNotification(Idea::factory()->for($this->user)->create());
    $otherUsers = storeIdeaPublishedNotification(Idea::factory()->create());

    $response = $this->actingAs($this->user)->post(route('notifications.read-all'));

    $response->assertRedirect(route('notifications.index'));
    $response->assertSessionHas('success', 'All notifications marked as read.');
    expect($first->fresh()->read())->toBeTrue()
        ->and($second->fresh()->read())->toBeTrue()
        ->and($otherUsers->fresh()->read())->toBeFalse();
});
