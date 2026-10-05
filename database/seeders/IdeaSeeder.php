<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
{
    /**
     * Seed 20 fully detailed ideas for the given user.
     */
    public function run(User $user): void
    {
        Idea::factory()
            ->for($user)
            ->withImage()
            ->count(20)
            ->sequence(
                fn (): array => ['status' => IdeaStatus::Pending],
                fn (): array => ['status' => IdeaStatus::InProgress],
                fn (): array => ['status' => IdeaStatus::Completed],
            )
            ->state(fn (): array => [
                'title' => rtrim(fake()->sentence(4), '.'),
                'description' => fake()->paragraphs(2, true),
                'links' => collect(range(1, fake()->numberBetween(1, 3)))->map(fn (): string => fake()->url())->all(),
                'created_at' => $createdAt = fake()->dateTimeBetween('-3 months', '-1 week'),
                'updated_at' => fake()->dateTimeBetween($createdAt),
            ])
            ->create()
            ->each(function (Idea $idea): void {
                $stepCount = fake()->numberBetween(3, 5);

                $completedStepCount = match ($idea->status) {
                    IdeaStatus::Pending => 0,
                    IdeaStatus::InProgress => fake()->numberBetween(1, $stepCount - 1),
                    IdeaStatus::Completed => $stepCount,
                };

                Step::factory()
                    ->for($idea)
                    ->count($stepCount)
                    ->sequence(fn ($sequence): array => ['is_completed' => $sequence->index < $completedStepCount])
                    ->create();
            });
    }
}
