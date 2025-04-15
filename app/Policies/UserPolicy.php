<?php

namespace App\Policies;

use App\Models\Enums\RoleEnum;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleEnum::Administrator) || $user->hasRole(RoleEnum::User);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasRole(RoleEnum::Administrator) || $user->hasRole(RoleEnum::User);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(RoleEnum::Administrator);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool {
        if ($user->id == $model->id || $user->hasRole(RoleEnum::Administrator)) {
            return true;
        }

        if ($user->hasRole(RoleEnum::Administrator)) {
            return $user->id == $model->id
                && !$model->hasRole(RoleEnum::Administrator);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->hasRole(RoleEnum::Administrator)) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
