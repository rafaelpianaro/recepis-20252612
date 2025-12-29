<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can view the user list.
     */
    public function viewAny(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Only administrators can view user list.');
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Only administrators can create users.');
    }

    /**
     * Determine if the user can update other users.
     */
    public function update(User $user, User $model): Response
    {
        // Only admins can update users
        if (! $user->isAdmin()) {
            return Response::deny('Only administrators can update users.');
        }

        // Admins cannot update themselves through admin panel
        if ($user->id === $model->id) {
            return Response::deny('You cannot edit your own account through this interface.');
        }

        return Response::allow();
    }

    /**
     * Determine if the user can update their own profile.
     */
    public function updateProfile(User $user, User $model): Response
    {
        return $user->id === $model->id
            ? Response::allow()
            : Response::deny('You cannot update another user\'s profile.');
    }

    /**
     * Determine if the user can change roles.
     */
    public function changeRole(User $user, User $model): Response
    {
        // Only admins can change roles
        if (! $user->isAdmin()) {
            return Response::deny('Only administrators can change user roles.');
        }

        // Admins cannot change their own role
        if ($user->id === $model->id) {
            return Response::deny('You cannot change your own role.');
        }

        return Response::allow();
    }

    /**
     * Determine if the user can delete users.
     */
    public function delete(User $user, User $model): Response
    {
        // Admins can delete users
        if ($user->isAdmin() && $user->id !== $model->id) {
            return Response::allow();
        }

        // Users can delete their own account (from profile settings)
        if ($user->id === $model->id) {
            return Response::allow();
        }

        return Response::deny('You cannot delete this user.');
    }
}
