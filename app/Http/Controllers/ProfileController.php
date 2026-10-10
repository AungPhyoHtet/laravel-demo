<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Notifications\ProfileUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the authenticated user's profile.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = $request->validated('password');
        }

        $changedFields = array_values(array_filter(['name', 'email', 'password'], fn (string $field): bool => $user->isDirty($field)));
        $previousEmail = $user->getOriginal('email');

        $user->save();

        if ($changedFields !== []) {
            $user->notify(new ProfileUpdated($changedFields));
        }

        if (in_array('email', $changedFields, true)) {
            Notification::route('mail', $previousEmail)->notify(new ProfileUpdated($changedFields));
        }

        return to_route('profile.edit')->with('success', 'Profile updated.');
    }
}
