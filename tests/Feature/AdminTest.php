<?php

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests to the login page', function () {
    $response = $this->get(route('admin'));

    $response->assertRedirect(route('login'));
});

it('returns not found for non-admin users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin'));

    $response->assertNotFound();
});

it('shows all ideas to admins', function () {
    $admin = User::factory()->admin()->create();
    $idea = Idea::factory()->create(['description' => 'Someone else entirely']);

    $response = $this->actingAs($admin)->get(route('admin'));

    $response->assertOk();
    $response->assertSee($idea->description);
    $response->assertSee($idea->user->name);
});

it('shows the admin nav link only to admins', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($user)->get(route('ideas.index'))
        ->assertDontSee(route('admin'));

    $this->actingAs($admin)->get(route('ideas.index'))
        ->assertSee(route('admin'));
});
