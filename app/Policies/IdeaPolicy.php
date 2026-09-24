<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    /**
     * Determine whether the user can view the idea.
     */
    public function view(User $user, Idea $idea): bool
    {
        return $idea->user()->is($user);
    }

    /**
     * Determine whether the user can update the idea.
     */
    public function update(User $user, Idea $idea): bool
    {
        return $idea->user()->is($user);
    }

    /**
     * Determine whether the user can delete the idea.
     */
    public function delete(User $user, Idea $idea): bool
    {
        return $idea->user()->is($user);
    }
}
