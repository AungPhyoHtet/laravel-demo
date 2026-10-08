<?php

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('redirects guests to the login page', function (): void {
    $step = Step::factory()->create();

    $response = $this->patch(route('steps.update', $step), ['is_completed' => '1']);

    $response->assertRedirect(route('login'));
    expect($step->fresh()->is_completed)->toBeFalse();
});

it('updates the completion status and redirects to the idea', function (bool $wasCompleted, string $submitted, bool $isCompleted): void {
    $idea = Idea::factory()->for($this->user)->create();
    $step = Step::factory()->for($idea)->create(['is_completed' => $wasCompleted]);

    $response = $this->actingAs($this->user)->patch(route('steps.update', $step), ['is_completed' => $submitted]);

    $response->assertRedirect(route('ideas.show', $idea));
    expect($step->fresh()->is_completed)->toBe($isCompleted);
})->with([
    'check' => [false, '1', true],
    'uncheck' => [true, '0', false],
]);

it('rejects a missing or non-boolean completion status', function (array $payload): void {
    $step = Step::factory()->for(Idea::factory()->for($this->user))->create();

    $response = $this->actingAs($this->user)->patch(route('steps.update', $step), $payload);

    $response->assertInvalid(['is_completed']);
    expect($step->fresh()->is_completed)->toBeFalse();
})->with([
    'missing' => [[]],
    'non-boolean' => [['is_completed' => 'yes please']],
]);

it('forbids updating a step of another user\'s idea', function (): void {
    $step = Step::factory()->create();

    $response = $this->actingAs($this->user)->patch(route('steps.update', $step), ['is_completed' => '1']);

    $response->assertForbidden();
    expect($step->fresh()->is_completed)->toBeFalse();
});
