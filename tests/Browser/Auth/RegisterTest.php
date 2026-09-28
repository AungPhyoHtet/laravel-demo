<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('registers a new user and redirects home', function (): void {
    visit(route('register'))
        ->assertSee('Create an account to start sharing your ideas.')
        ->fill('name', 'Jane Doe')
        ->fill('email', 'jane@example.com')
        ->fill('password', 'password123')
        ->fill('password_confirmation', 'password123')
        ->press('main button[type="submit"]')
        ->assertPathIs('/')
        ->assertSee('Log Out')
        ->assertNoJavaScriptErrors();

    $user = User::query()->where('email', 'jane@example.com')->sole();

    expect($user->name)->toBe('Jane Doe')
        ->and(Auth::id())->toBe($user->id);
});

it('shows validation messages under the fields and hides the password hint', function (): void {
    visit(route('register'))
        ->assertSee('Use at least 8 characters.')
        ->fill('name', 'Jane Doe')
        ->fill('email', 'not-an-email')
        ->fill('password', 'short')
        ->fill('password_confirmation', 'different')
        ->press('main button[type="submit"]')
        ->assertPathIs('/register')
        ->assertSee('The email field must be a valid email address.')
        ->assertSee('The password field confirmation does not match.')
        ->assertDontSee('Use at least 8 characters.')
        ->assertValue('name', 'Jane Doe')
        ->assertNoJavaScriptErrors();

    expect(User::query()->count())->toBe(0);
});
