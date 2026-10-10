<?php

namespace App\Models;

use App\Enums\IdeaStatus;
use Database\Factories\IdeaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'description',
    'links',
    'status',
    'image_path',
])]
class Idea extends Model
{
    /** @use HasFactory<IdeaFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => IdeaStatus::Pending->value,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'links' => 'array',
            'status' => IdeaStatus::class,
        ];
    }

    /**
     * Get the description rendered from Markdown to HTML, with raw HTML escaped and unsafe links removed.
     *
     * @return Attribute<string, never>
     */
    protected function descriptionHtml(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Str::markdown($this->description ?? '', [
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
            ]),
        );
    }

    /**
     * Get the description as plain text with the Markdown formatting removed, for previews.
     *
     * @return Attribute<string, never>
     */
    protected function descriptionText(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim(html_entity_decode(strip_tags($this->description_html), ENT_QUOTES | ENT_HTML5)),
        );
    }

    /**
     * Get the user who owns the idea.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the steps for the idea.
     *
     * @return HasMany<Step, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    /**
     * Sync the idea's steps to the given list, keeping the completion state of existing steps.
     *
     * @param  list<array{id?: int|string|null, description: string}>  $steps
     */
    public function syncSteps(array $steps): void
    {
        $this->steps()->whereNotIn('id', array_filter(array_column($steps, 'id')))->delete();

        foreach ($steps as $step) {
            if (filled($step['id'] ?? null)) {
                $this->steps()->whereKey($step['id'])->update(['description' => $step['description']]);
            } else {
                $this->steps()->create(['description' => $step['description']]);
            }
        }
    }
}
