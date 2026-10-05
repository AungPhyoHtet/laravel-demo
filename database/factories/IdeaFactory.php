<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'links' => [fake()->url()],
            'status' => IdeaStatus::Pending,
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the idea has a generated image stored on the public disk.
     */
    public function withImage(): static
    {
        return $this->state(function (array $attributes): array {
            $image = imagecreatetruecolor(1200, 800);
            imagefill($image, 0, 0, imagecolorallocate($image, fake()->numberBetween(60, 220), fake()->numberBetween(60, 220), fake()->numberBetween(60, 220)));

            ob_start();
            imagejpeg($image);
            $contents = ob_get_clean();

            $path = 'ideas/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($path, $contents);

            return ['image_path' => $path];
        });
    }
}
