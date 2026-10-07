<?php

use App\Enums\IdeaStatus;
use App\Jobs\SendIdeaPublishedNotification;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

/**
 * The browser plugin's test server only parses URL-encoded bodies and drops multipart ones,
 * so submit without the file-upload encoding. Image uploads are covered by the feature tests.
 */
const SUBMIT_WITHOUT_MULTIPART = "document.querySelector('main form').removeAttribute('enctype')";

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('creates an idea with the added link and step rows', function (): void {
    Queue::fake();

    $page = visit(route('ideas.create'))
        ->fill('title', 'Mousetrap')
        ->fill('description', 'Build a better mousetrap')
        ->select('status', IdeaStatus::InProgress->value)
        ->fill('input[name="links[]"] >> nth=0', 'https://example.com/mousetrap')
        ->click('Add link')
        ->fill('input[name="links[]"] >> nth=1', 'https://example.com/cheese')
        ->fill('input[name="steps[0][description]"]', 'Sketch it')
        ->click('Add step')
        ->fill('input[name="steps[1][description]"]', 'Build it');

    $page->script(SUBMIT_WITHOUT_MULTIPART);

    $page->press('Create')
        ->assertPathIs('/ideas')
        ->assertSee('Idea created.')
        ->assertSee('Mousetrap')
        ->assertNoJavaScriptErrors();

    $idea = Idea::sole();

    expect($idea)
        ->user_id->toBe($this->user->id)
        ->title->toBe('Mousetrap')
        ->description->toBe('Build a better mousetrap')
        ->status->toBe(IdeaStatus::InProgress)
        ->links->toBe(['https://example.com/mousetrap', 'https://example.com/cheese']);
    expect($idea->steps()->pluck('description')->all())->toBe(['Sketch it', 'Build it']);
    Queue::assertPushed(SendIdeaPublishedNotification::class);
});

it('keeps the added link and step rows and shows errors when validation fails', function (): void {
    $page = visit(route('ideas.create'))
        ->fill('description', 'Too short')
        ->fill('input[name="links[]"] >> nth=0', 'https://example.com/mousetrap')
        ->click('Add link')
        ->fill('input[name="links[]"] >> nth=1', 'ftp://example.com/cheese')
        ->fill('input[name="steps[0][description]"]', 'Sketch it');

    $page->script(SUBMIT_WITHOUT_MULTIPART);

    $page->press('Create')
        ->assertPathIs('/ideas/create')
        ->assertSee('The title field is required.')
        ->assertSee('The description field must be at least 10 characters.')
        ->assertSee('The link field must be a valid URL.')
        ->assertCount('input[name="links[]"]', 2)
        ->assertValue('input[name="links[]"] >> nth=1', 'ftp://example.com/cheese')
        ->assertValue('input[name="steps[0][description]"]', 'Sketch it')
        ->assertNoJavaScriptErrors();

    expect(Idea::count())->toBe(0);
});
