<?php

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

it('checks and unchecks a step from the idea page', function (): void {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();
    $step = Step::factory()->for($idea)->create(['description' => 'Sketch it']);

    $this->actingAs($user);

    $page = visit(route('ideas.show', $idea))
        ->assertSee('0 / 1 done')
        ->check("#step-{$step->id}")
        ->assertSee('1 / 1 done')
        ->assertChecked("#step-{$step->id}");

    expect($step->fresh()->is_completed)->toBeTrue();

    $page->uncheck("#step-{$step->id}")
        ->assertSee('0 / 1 done')
        ->assertNotChecked("#step-{$step->id}")
        ->assertNoJavaScriptErrors();

    expect($step->fresh()->is_completed)->toBeFalse();
});
