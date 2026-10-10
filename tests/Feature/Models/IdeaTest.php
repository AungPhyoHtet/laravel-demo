<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('defaults the status to pending when none is given', function (): void {
    $idea = new Idea(['title' => 'Launch a newsletter']);

    $status = $idea->status;

    expect($status)->toBe(IdeaStatus::Pending);
});

it('restores the stored status as an enum', function (): void {
    $idea = Idea::factory()->create(['status' => IdeaStatus::InProgress]);

    $status = $idea->fresh()->status;

    expect($status)->toBe(IdeaStatus::InProgress);
});

it('restores the stored links as an array', function (): void {
    $idea = Idea::factory()->create([
        'links' => ['https://laravel.com', 'https://pestphp.com'],
    ]);

    $links = $idea->fresh()->links;

    expect($links)->toBe(['https://laravel.com', 'https://pestphp.com']);
});

it('returns only the steps that belong to the idea', function (): void {
    $idea = Idea::factory()->create();
    $steps = Step::factory()->count(2)->for($idea)->create();
    Step::factory()->create();

    $ideaSteps = $idea->steps;

    expect($ideaSteps->modelKeys())->toBe($steps->modelKeys());
});

it('deletes its steps when the idea is deleted', function (): void {
    $idea = Idea::factory()->create();
    $step = Step::factory()->for($idea)->create();

    $idea->delete();

    $this->assertModelMissing($step);
});

it('renders the description from Markdown to HTML', function (): void {
    $idea = new Idea(['description' => "## Goal\n\nBuild a **better** mousetrap\n\n- cheap\n- humane"]);

    $html = $idea->description_html;

    expect($html)
        ->toContain('<h2>Goal</h2>')
        ->toContain('<strong>better</strong>')
        ->toContain('<li>cheap</li>');
});

it('escapes raw HTML and drops unsafe links in the rendered description', function (): void {
    $idea = new Idea(['description' => "Hi <script>alert('xss')</script>\n\n[bad](javascript:alert(1)) [good](https://laravel.com)"]);

    $html = $idea->description_html;

    expect($html)
        ->toContain('Hi &lt;script&gt;')
        ->not->toContain('<script>')
        ->toContain('<a>bad</a>')
        ->not->toContain('javascript:')
        ->toContain('<a href="https://laravel.com">good</a>');
});

it('strips the Markdown formatting from the plain-text description', function (): void {
    $idea = new Idea(['description' => "## Goal\n\nBuild a **better** mousetrap & \"more\""]);

    $text = $idea->description_text;

    expect($text)->toBe("Goal\nBuild a better mousetrap & \"more\"");
});
