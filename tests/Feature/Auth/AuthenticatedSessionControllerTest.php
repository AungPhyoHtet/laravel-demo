<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('create', function (): void {
    it('renders the login form', function (): void {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Log In');
    });

    it('redirects an authenticated user to home', function (): void {
        $this->actingAs(User::factory()->create());

        $response = $this->get(route('login'));

        $response->assertRedirect(route('home'));
    });
});

describe('store', function (): void {
    it('logs the user in and redirects home', function (): void {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        expect(Auth::check())->toBeTrue();
        expect(Auth::id())->toBe($user->id);
    });

    it('rejects invalid credentials', function (): void {
        $user = User::factory()->create(['password' => Hash::make('password123')]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        expect(Auth::check())->toBeFalse();
    });
});

describe('destroy', function (): void {
    it('logs the user out and redirects home', function (): void {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        expect(Auth::check())->toBeFalse();
    });

    it('redirects a guest to login', function (): void {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
    });
});
