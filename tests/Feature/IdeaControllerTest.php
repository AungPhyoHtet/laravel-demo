<?php

use App\Enums\IdeaStatus;
use App\Jobs\SendIdeaPublishedNotification;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function ideaPayload(array $overrides = []): array
{
    return [
        'title' => 'Mousetrap',
        'description' => 'Build a better mousetrap',
        'status' => IdeaStatus::Pending->value,
        ...$overrides,
    ];
}

it('redirects guests to the login page', function (string $method, string $routeName): void {
    $idea = Idea::factory()->create();

    $response = $this->{$method}(route($routeName, $idea));

    $response->assertRedirect(route('login'));
})->with([
    'index' => ['get', 'ideas.index'],
    'create' => ['get', 'ideas.create'],
    'store' => ['post', 'ideas.store'],
    'show' => ['get', 'ideas.show'],
    'edit' => ['get', 'ideas.edit'],
    'update' => ['put', 'ideas.update'],
    'destroy' => ['delete', 'ideas.destroy'],
]);

describe('index', function (): void {
    it('displays only the ideas owned by the user', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);
        $otherIdea = Idea::factory()->create(['description' => 'Someone else entirely']);

        $response = $this->actingAs($this->user)->get(route('ideas.index'));

        $response->assertOk();
        $response->assertSee($idea->description);
        $response->assertDontSee($otherIdea->description);
    });

    it('shows the full idea details on each card', function (): void {
        $idea = Idea::factory()->for($this->user)->create([
            'title' => 'Mousetrap',
            'status' => IdeaStatus::InProgress,
            'links' => ['https://example.com/mousetrap'],
        ]);
        Step::factory()->for($idea)->create(['description' => 'Sketch it', 'is_completed' => true]);
        Step::factory()->for($idea)->create(['description' => 'Build it']);

        $response = $this->actingAs($this->user)->get(route('ideas.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Mousetrap', 'In Progress', $idea->description, 'https://example.com/mousetrap', '1 / 2 done', 'Sketch it', 'Build it']);
    });

    it('eager loads the steps for every idea', function (): void {
        Idea::factory()->for($this->user)->has(Step::factory()->count(2))->count(3)->create();
        Model::preventLazyLoading();

        try {
            $response = $this->actingAs($this->user)->get(route('ideas.index'));
        } finally {
            Model::preventLazyLoading(false);
        }

        $response->assertOk();
    });

    it('filters the ideas by status', function (IdeaStatus $status): void {
        foreach (IdeaStatus::cases() as $case) {
            Idea::factory()->for($this->user)->create(['description' => "Idea that is {$case->value}", 'status' => $case]);
        }

        $response = $this->actingAs($this->user)->get(route('ideas.index', ['status' => $status->value]));

        $response->assertViewHas('ideas', fn ($ideas): bool => $ideas->pluck('status')->all() === [$status]);
        $response->assertSee("Idea that is {$status->value}");
    })->with(IdeaStatus::cases());

    it('redirects to the unfiltered list with an error when the status filter is invalid', function (): void {
        $response = $this->actingAs($this->user)
            ->from(route('ideas.index', ['status' => 'in_progresssdsfsd']))
            ->get(route('ideas.index', ['status' => 'in_progresssdsfsd']));

        $response->assertRedirect(route('ideas.index'));
        $response->assertSessionHasErrors(['status' => 'The selected status filter is invalid.']);
    });

    it('shows the invalid status filter error above the filters', function (): void {
        $response = $this->actingAs($this->user)
            ->followingRedirects()
            ->get(route('ideas.index', ['status' => 'in_progresssdsfsd']));

        $response->assertSeeInOrder(['The selected status filter is invalid.', 'Filter ideas by status']);
    });

    it('counts the user\'s ideas for each filter tab', function (): void {
        Idea::factory()->for($this->user)->count(2)->create(['status' => IdeaStatus::Pending]);
        Idea::factory()->for($this->user)->create(['status' => IdeaStatus::Completed]);
        Idea::factory()->create(['status' => IdeaStatus::Pending]);

        $response = $this->actingAs($this->user)->get(route('ideas.index', ['status' => 'completed']));

        $response->assertSeeTextInOrder(['All', '3', 'Pending', '2', 'In Progress', '0', 'Completed', '1']);
    });

    it('keeps the status filter in the pagination links', function (): void {
        Idea::factory()->for($this->user)->count(11)->create(['status' => IdeaStatus::Completed]);

        $response = $this->actingAs($this->user)->get(route('ideas.index', ['status' => 'completed']));

        $response->assertSee(route('ideas.index', ['status' => 'completed', 'page' => 2]));
    });

    it('says when no ideas match the status filter', function (): void {
        Idea::factory()->for($this->user)->create(['status' => IdeaStatus::Pending]);

        $response = $this->actingAs($this->user)->get(route('ideas.index', ['status' => 'completed']));

        $response->assertSee('No completed ideas.');
    });
});

describe('create', function (): void {
    it('renders the idea form', function (): void {
        $response = $this->actingAs($this->user)->get(route('ideas.create'));

        $response->assertOk();
        $response->assertSee('Description');
    });
});

describe('store', function (): void {
    it('creates the idea for the user and redirects to the index', function (): void {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), ideaPayload());

        $response->assertRedirect(route('ideas.index'));
        $response->assertSessionHas('success', 'Idea created.');
        expect($this->user->ideas()->where('description', 'Build a better mousetrap')->exists())->toBeTrue();
    });

    it('queues the idea published notification', function (): void {
        Queue::fake();

        $this->actingAs($this->user)->post(route('ideas.store'), ideaPayload());

        $idea = $this->user->ideas()->sole();

        Queue::assertPushed(SendIdeaPublishedNotification::class, fn (SendIdeaPublishedNotification $job) => $job->idea->is($idea));
    });

    it('rejects a missing description', function (): void {
        Queue::fake();

        $response = $this->actingAs($this->user)->post(route('ideas.store'), []);

        $response->assertSessionHasErrors('description');
        expect(Idea::count())->toBe(0);
        Queue::assertNothingPushed();
    });

    it('rejects a description shorter than 10 characters', function (): void {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), [
            'description' => 'too short',
        ]);

        $response->assertSessionHasErrors([
            'description' => 'The description field must be at least 10 characters.',
        ]);
        expect(Idea::count())->toBe(0);
    });
});

describe('store details', function (): void {
    it('saves the title, status, links, steps and image', function (): void {
        Storage::fake('public');

        $this->actingAs($this->user)->post(route('ideas.store'), ideaPayload([
            'status' => IdeaStatus::InProgress->value,
            'links' => ['https://example.com', ''],
            'steps' => [['id' => '', 'description' => 'Sketch it'], ['id' => '', 'description' => ''], ['id' => '', 'description' => 'Build it']],
            'image' => UploadedFile::fake()->image('mousetrap.jpg'),
        ]))->assertRedirect(route('ideas.index'));

        $idea = $this->user->ideas()->sole();

        expect($idea->title)->toBe('Mousetrap')
            ->and($idea->status)->toBe(IdeaStatus::InProgress)
            ->and($idea->links)->toBe(['https://example.com'])
            ->and($idea->steps()->pluck('description')->all())->toBe(['Sketch it', 'Build it']);
        Storage::disk('public')->assertExists($idea->image_path);
    });

    it('rejects invalid fields', function (array $overrides, string $errorKey): void {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), ideaPayload($overrides));

        $response->assertSessionHasErrors($errorKey);
        expect(Idea::count())->toBe(0);
    })->with([
        'missing title' => [['title' => ''], 'title'],
        'unknown status' => [['status' => 'archived'], 'status'],
        'invalid link' => [['links' => ['not-a-url']], 'links.0'],
        'non-http link' => [['links' => ['javascript:alert(1)']], 'links.0'],
        'step with an id' => [['steps' => [['id' => 1, 'description' => 'Sketch it']]], 'steps.0.id'],
        'too long step' => [['steps' => [['description' => str_repeat('a', 256)]]], 'steps.0.description'],
        'non-image file' => [['image' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf')], 'image'],
        'image over 2 MB' => [['image' => UploadedFile::fake()->image('big.jpg')->size(2049)], 'image'],
    ]);

    it('rejects a completion flag submitted with a step', function (): void {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), ideaPayload([
            'steps' => [['description' => 'Sketch it', 'is_completed' => true]],
        ]));

        $response->assertSessionHasErrors('steps.0');
        expect(Step::count())->toBe(0);
    });
});

describe('show', function (): void {
    it('renders the description as formatted Markdown', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => "## Goal\n\nBuild a **better** mousetrap"]);

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertSee('<div class="prose max-w-none"><h2>Goal</h2>', false);
        $response->assertSee('<strong>better</strong>', false);
    });

    it('displays the idea', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertOk();
        $response->assertSee($idea->description);
    });

    it('shows the full idea details', function (): void {
        $this->travelTo('2026-03-14 09:00:00');
        $idea = Idea::factory()->for($this->user)->create([
            'description' => 'Build a better mousetrap',
            'links' => ['https://example.com/mousetrap'],
            'image_path' => 'ideas/mousetrap.jpg',
        ]);
        Step::factory()->for($idea)->create(['description' => 'Sketch it', 'is_completed' => true]);
        Step::factory()->for($idea)->create(['description' => 'Build it']);
        $this->travelBack();

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertSeeInOrder([
            Storage::disk('public')->url('ideas/mousetrap.jpg'),
            'Created Sat, Mar 14, 2026',
            'Build a better mousetrap',
            'https://example.com/mousetrap',
            '1 / 2 done',
            'Sketch it',
            'Build it',
        ]);
    });

    it('says when the idea has no links or steps', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['links' => null]);

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertSeeInOrder(['No links added.', 'No steps added.']);
    });

    it('links back to the list and to the edit and delete actions', function (): void {
        $idea = Idea::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertSeeInOrder([
            'href="'.route('ideas.index').'"',
            'href="'.route('ideas.edit', $idea).'"',
            'action="'.route('ideas.destroy', $idea).'"',
            'name="_method" value="DELETE"',
        ], false);
    });

    it('forbids viewing another user\'s idea', function (): void {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertForbidden();
    });
});

describe('edit', function (): void {
    it('renders the idea form with the current description', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->get(route('ideas.edit', $idea));

        $response->assertOk();
        $response->assertSee($idea->description);
    });

    it('forbids editing another user\'s idea', function (): void {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->get(route('ideas.edit', $idea));

        $response->assertForbidden();
    });
});

describe('update', function (): void {
    it('updates the idea and redirects to the index', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), ideaPayload([
            'description' => 'Build an even better mousetrap',
        ]));

        $response->assertRedirect(route('ideas.index'));
        $response->assertSessionHas('success', 'Idea updated.');
        expect($idea->fresh()->description)->toBe('Build an even better mousetrap');
    });

    it('rejects a description shorter than 10 characters', function (): void {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), [
            'description' => 'too short',
        ]);

        $response->assertSessionHasErrors('description');
        expect($idea->fresh()->description)->toBe('Build a better mousetrap');
    });

    it('forbids updating another user\'s idea', function (): void {
        $idea = Idea::factory()->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), [
            'description' => 'Build an even better mousetrap',
        ]);

        $response->assertForbidden();
        expect($idea->fresh()->description)->toBe('Build a better mousetrap');
    });
});

describe('update details', function (): void {
    it('syncs steps, keeping the completion state of kept steps', function (): void {
        $idea = Idea::factory()->for($this->user)->create();
        $keptStep = Step::factory()->for($idea)->create(['description' => 'Sketch it', 'is_completed' => true]);
        $removedStep = Step::factory()->for($idea)->create();

        $this->actingAs($this->user)->put(route('ideas.update', $idea), ideaPayload([
            'steps' => [
                ['id' => $keptStep->id, 'description' => 'Sketch it properly'],
                ['id' => null, 'description' => 'Build it'],
            ],
        ]))->assertRedirect(route('ideas.index'));

        expect($keptStep->fresh())
            ->description->toBe('Sketch it properly')
            ->is_completed->toBeTrue()
            ->and(Step::find($removedStep->id))->toBeNull()
            ->and($idea->steps()->pluck('description')->all())->toBe(['Sketch it properly', 'Build it']);
    });

    it('rejects a step belonging to another idea', function (): void {
        $idea = Idea::factory()->for($this->user)->create();
        $otherStep = Step::factory()->create(['description' => 'Not yours']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), ideaPayload([
            'steps' => [['id' => $otherStep->id, 'description' => 'Hijacked']],
        ]));

        $response->assertSessionHasErrors('steps.0.id');
        expect($otherStep->fresh()->description)->toBe('Not yours');
    });

    it('replaces the image and deletes the previous file', function (): void {
        Storage::fake('public');
        $previousPath = UploadedFile::fake()->image('old.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->for($this->user)->create(['image_path' => $previousPath]);

        $this->actingAs($this->user)->put(route('ideas.update', $idea), ideaPayload([
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]))->assertRedirect(route('ideas.index'));

        Storage::disk('public')->assertMissing($previousPath);
        Storage::disk('public')->assertExists($idea->fresh()->image_path);
    });

    it('keeps the current image when no new image is uploaded', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('old.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->for($this->user)->create(['image_path' => $path]);

        $this->actingAs($this->user)->put(route('ideas.update', $idea), ideaPayload());

        expect($idea->fresh()->image_path)->toBe($path);
        Storage::disk('public')->assertExists($path);
    });
});

describe('destroy', function (): void {
    it('deletes the idea and redirects to the index', function (): void {
        $idea = Idea::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->delete(route('ideas.destroy', $idea));

        $response->assertRedirect(route('ideas.index'));
        $response->assertSessionHas('success', 'Idea deleted.');
        expect(Idea::find($idea->id))->toBeNull();
    });

    it('deletes the idea image', function (): void {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('old.jpg')->store('ideas', 'public');
        $idea = Idea::factory()->for($this->user)->create(['image_path' => $path]);

        $this->actingAs($this->user)->delete(route('ideas.destroy', $idea));

        Storage::disk('public')->assertMissing($path);
    });

    it('forbids deleting another user\'s idea', function (): void {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('ideas.destroy', $idea));

        $response->assertForbidden();
        expect(Idea::find($idea->id))->not->toBeNull();
    });
});
