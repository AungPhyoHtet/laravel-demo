<?php

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('redirects guests to the login page', function (string $method, string $routeName) {
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

describe('index', function () {
    it('displays only the ideas owned by the user', function () {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);
        $otherIdea = Idea::factory()->create(['description' => 'Someone else entirely']);

        $response = $this->actingAs($this->user)->get(route('ideas.index'));

        $response->assertOk();
        $response->assertSee($idea->description);
        $response->assertDontSee($otherIdea->description);
    });
});

describe('create', function () {
    it('renders the idea form', function () {
        $response = $this->actingAs($this->user)->get(route('ideas.create'));

        $response->assertOk();
        $response->assertSee('Description');
    });
});

describe('store', function () {
    it('creates the idea for the user and redirects to the index', function () {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), [
            'description' => 'Build a better mousetrap',
        ]);

        $response->assertRedirect(route('ideas.index'));
        expect($this->user->ideas()->where('description', 'Build a better mousetrap')->exists())->toBeTrue();
    });

    it('rejects a missing description', function () {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), []);

        $response->assertSessionHasErrors('description');
        expect(Idea::count())->toBe(0);
    });

    it('rejects a description shorter than 10 characters', function () {
        $response = $this->actingAs($this->user)->post(route('ideas.store'), [
            'description' => 'too short',
        ]);

        $response->assertSessionHasErrors([
            'description' => 'The description field must be at least 10 characters.',
        ]);
        expect(Idea::count())->toBe(0);
    });
});

describe('show', function () {
    it('displays the idea', function () {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertOk();
        $response->assertSee($idea->description);
    });

    it('forbids viewing another user\'s idea', function () {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->get(route('ideas.show', $idea));

        $response->assertForbidden();
    });
});

describe('edit', function () {
    it('renders the idea form with the current description', function () {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->get(route('ideas.edit', $idea));

        $response->assertOk();
        $response->assertSee($idea->description);
    });

    it('forbids editing another user\'s idea', function () {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->get(route('ideas.edit', $idea));

        $response->assertForbidden();
    });
});

describe('update', function () {
    it('updates the idea and redirects to the index', function () {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), [
            'description' => 'Build an even better mousetrap',
        ]);

        $response->assertRedirect(route('ideas.index'));
        expect($idea->fresh()->description)->toBe('Build an even better mousetrap');
    });

    it('rejects a description shorter than 10 characters', function () {
        $idea = Idea::factory()->for($this->user)->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), [
            'description' => 'too short',
        ]);

        $response->assertSessionHasErrors('description');
        expect($idea->fresh()->description)->toBe('Build a better mousetrap');
    });

    it('forbids updating another user\'s idea', function () {
        $idea = Idea::factory()->create(['description' => 'Build a better mousetrap']);

        $response = $this->actingAs($this->user)->put(route('ideas.update', $idea), [
            'description' => 'Build an even better mousetrap',
        ]);

        $response->assertForbidden();
        expect($idea->fresh()->description)->toBe('Build a better mousetrap');
    });
});

describe('destroy', function () {
    it('deletes the idea and redirects to the index', function () {
        $idea = Idea::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->delete(route('ideas.destroy', $idea));

        $response->assertRedirect(route('ideas.index'));
        expect(Idea::find($idea->id))->toBeNull();
    });

    it('forbids deleting another user\'s idea', function () {
        $idea = Idea::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('ideas.destroy', $idea));

        $response->assertForbidden();
        expect(Idea::find($idea->id))->not->toBeNull();
    });
});
