<?php

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('restores the completion flag as a boolean', function (): void {
    $step = Step::factory()->completed()->create();

    $isCompleted = $step->fresh()->is_completed;

    expect($isCompleted)->toBeTrue();
});

it('belongs to its idea', function (): void {
    $idea = Idea::factory()->create();
    $step = Step::factory()->for($idea)->create();

    $stepIdea = $step->idea;

    expect($stepIdea->is($idea))->toBeTrue();
});
