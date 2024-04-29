<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view-any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        if ($role->name === 'super_admin') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->name == 'super_admin') {
            return false;
        }

        return $user->can('roles.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->name === 'super_admin') {
            return false;
        }

        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can delete many models.
     */
    public function deleteMany(User $user): bool
    {
        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can assign permissions to the model.
     */
    public function assignPermissions(User $user): bool
    {
        return $user->can('roles.permissions.assign');
    }
}
