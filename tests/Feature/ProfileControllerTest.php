<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);
});

it('redirects guests to the login page', function (string $method, string $routeName): void {
    $response = $this->{$method}(route($routeName));

    $response->assertRedirect(route('login'));
})->with([
    'edit' => ['get', 'profile.edit'],
    'update' => ['patch', 'profile.update'],
]);

describe('edit', function (): void {
    it('renders the form filled with the user\'s details', function (): void {
        $response = $this->actingAs($this->user)->get(route('profile.edit'));

        $response->assertSee('Edit Profile');
        $response->assertSee('value="Ada Lovelace"', false);
        $response->assertSee('value="ada@example.com"', false);
    });
});

describe('update', function (): void {
    it('updates the name and email and redirects back with a flash message', function (): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success', 'Profile updated.');

        $user = $this->user->fresh();
        expect($user->name)->toBe('Grace Hopper')
            ->and($user->email)->toBe('grace@example.com')
            ->and($user->email_verified_at)->toBeNull()
            ->and(Hash::check('password', $user->password))->toBeTrue();
    });

    it('keeps the email verification when the email is unchanged', function (): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Grace Hopper',
            'email' => 'ada@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        expect($this->user->fresh()->email_verified_at)->not->toBeNull();
    });

    it('changes the password when the current password is correct', function (): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'current_password' => 'password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasNoErrors();
        expect(Hash::check('new-password123', $this->user->fresh()->password))->toBeTrue();
    });

    it('rejects a password change without the correct current password', function (array $currentPassword, string $message): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            ...$currentPassword,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasErrors(['current_password' => $message]);
        expect(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
    })->with([
        'missing' => [[], 'The current password field is required when password is present.'],
        'wrong' => [['current_password' => 'not-my-password'], 'The password is incorrect.'],
    ]);

    it('rejects a mismatched password confirmation', function (): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'current_password' => 'password',
            'password' => 'new-password123',
            'password_confirmation' => 'not-the-same',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        expect(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
    });

    it('rejects an email taken by another user', function (): void {
        User::factory()->create(['email' => 'grace@example.com']);

        $response = $this->actingAs($this->user)->patch(route('profile.update'), [
            'name' => 'Ada Lovelace',
            'email' => 'grace@example.com',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        expect($this->user->fresh()->email)->toBe('ada@example.com');
    });

    it('requires a name and email', function (): void {
        $response = $this->actingAs($this->user)->patch(route('profile.update'), []);

        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
        ]);
        expect($this->user->fresh()->name)->toBe('Ada Lovelace');
    });
});
