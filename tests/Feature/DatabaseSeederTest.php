<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('seeds the test user with 20 fully detailed ideas', function (): void {
    Storage::fake('public');

    $this->seed();

    $user = User::where('email', 'test@example.com')->sole();
    $ideas = $user->ideas()->with('steps')->get();

    expect($ideas)->toHaveCount(20);
    expect($ideas->pluck('status')->unique()->values()->all())
        ->toEqualCanonicalizing(IdeaStatus::cases());

    $ideas->each(function (Idea $idea): void {
        expect($idea->title)->not->toBeEmpty()
            ->and($idea->links)->not->toBeEmpty()
            ->and($idea->steps)->not->toBeEmpty();

        Storage::disk('public')->assertExists($idea->image_path);
    });
});

it('matches the completed steps to each idea status', function (): void {
    Storage::fake('public');

    $this->seed();

    $completedStepRatios = Idea::with('steps')->get()->map(fn (Idea $idea): array => [
        $idea->status,
        $idea->steps->where('is_completed', true)->count(),
        $idea->steps->count(),
    ]);

    $completedStepRatios->each(function (array $ratio): void {
        [$status, $completedStepCount, $stepCount] = $ratio;

        match ($status) {
            IdeaStatus::Pending => expect($completedStepCount)->toBe(0),
            IdeaStatus::InProgress => expect($completedStepCount)->toBeGreaterThan(0)->toBeLessThan($stepCount),
            IdeaStatus::Completed => expect($completedStepCount)->toBe($stepCount),
        };
    });
});
