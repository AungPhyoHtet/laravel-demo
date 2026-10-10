<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('updates the profile and shows a success toast', function (): void {
    $user = User::factory()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);

    $this->actingAs($user);

    visit(route('home'))
        ->click('Profile')
        ->assertPathIs('/profile')
        ->assertValue('name', 'Ada Lovelace')
        ->assertValue('email', 'ada@example.com')
        ->fill('name', 'Grace Hopper')
        ->fill('email', 'grace@example.com')
        ->fill('current_password', 'password')
        ->fill('password', 'new-password123')
        ->fill('password_confirmation', 'new-password123')
        ->press('Save')
        ->assertPathIs('/profile')
        ->assertSee('Profile updated.')
        ->assertValue('name', 'Grace Hopper')
        ->assertValue('email', 'grace@example.com')
        ->assertValue('password', '')
        ->assertNoJavaScriptErrors();

    $user->refresh();

    expect($user->name)->toBe('Grace Hopper')
        ->and($user->email)->toBe('grace@example.com')
        ->and(Hash::check('new-password123', $user->password))->toBeTrue();
});

it('shows validation messages and keeps the entered values', function (): void {
    $user = User::factory()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);

    $this->actingAs($user);

    visit(route('profile.edit'))
        ->assertSee('Required only to change your password.')
        ->fill('name', 'Grace Hopper')
        ->fill('current_password', 'not-my-password')
        ->fill('password', 'new-password123')
        ->fill('password_confirmation', 'new-password123')
        ->press('Save')
        ->assertPathIs('/profile')
        ->assertSee('The password is incorrect.')
        ->assertDontSee('Required only to change your password.')
        ->assertValue('name', 'Grace Hopper')
        ->assertNoJavaScriptErrors();

    expect($user->fresh()->name)->toBe('Ada Lovelace');
});
