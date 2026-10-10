<?php

use App\Models\User;
use App\Notifications\ProfileUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists the changed fields and links to the profile page in the mail', function (): void {
    $user = User::factory()->create();

    $mail = (new ProfileUpdated(['email', 'password']))->toMail($user);

    $html = (string) $mail->render();

    expect($mail->subject)->toBe('Your profile has been updated');
    expect($html)
        ->toContain('>Email address</li>')
        ->toContain('>Password</li>')
        ->not->toContain('>Name</li>')
        ->toContain(route('profile.edit'));
});
