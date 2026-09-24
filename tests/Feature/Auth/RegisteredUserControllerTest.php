<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

describe('create', function () {
    it('renders the registration form', function () {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Register');
    });

    it('redirects an authenticated user to home', function () {
        $this->actingAs(User::factory()->create());

        $response = $this->get(route('register'));

        $response->assertRedirect(route('home'));
    });
});

describe('store', function () {
    it('creates the user, logs them in, and redirects home', function () {
        $response = $this->post(route('register'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        expect(Auth::check())->toBeTrue();

        $user = User::where('email', 'ada@example.com')->first();
        expect($user)->not->toBeNull();
        expect($user->name)->toBe('Ada Lovelace');
    });

    it('rejects a mismatched password confirmation', function () {
        $response = $this->post(route('register'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'not-the-same',
        ]);

        $response->assertSessionHasErrors('password');
        expect(User::where('email', 'ada@example.com')->exists())->toBeFalse();
    });

    it('rejects a duplicate email', function () {
        User::factory()->create(['email' => 'ada@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });
});
