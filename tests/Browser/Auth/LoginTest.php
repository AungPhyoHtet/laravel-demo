<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

it('logs the user in and redirects home', function (): void {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    visit(route('login'))
        ->assertSee('Welcome back. Enter your email and password to continue.')
        ->fill('email', $user->email)
        ->fill('password', 'password123')
        ->press('main button[type="submit"]')
        ->assertPathIs('/')
        ->assertSee('Log Out')
        ->assertNoJavaScriptErrors();

    expect(Auth::id())->toBe($user->id);
});

it('shows validation messages under the fields when submitted empty', function (): void {
    visit(route('login'))
        ->press('main button[type="submit"]')
        ->assertPathIs('/login')
        ->assertSee('The email field is required.')
        ->assertSee('The password field is required.')
        ->assertNoJavaScriptErrors();

    expect(Auth::check())->toBeFalse();
});

it('keeps the email and shows an error for invalid credentials', function (): void {
    $user = User::factory()->create(['password' => Hash::make('password123')]);

    visit(route('login'))
        ->fill('email', $user->email)
        ->fill('password', 'wrong-password')
        ->press('main button[type="submit"]')
        ->assertPathIs('/login')
        ->assertSee('The provided credentials do not match our records.')
        ->assertValue('email', $user->email)
        ->assertNoJavaScriptErrors();

    expect(Auth::check())->toBeFalse();
});
