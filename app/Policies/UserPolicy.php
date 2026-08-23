<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver usuarios');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('ver usuarios');
    }

    public function create(User $user): bool
    {
        return $user->can('crear usuarios');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('editar usuarios');
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('eliminar usuarios');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('eliminar usuarios');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('eliminar usuarios');
    }
}