<?php

namespace App\Policies;

use App\Models\FollowUp;
use App\Models\User;

class FollowUpPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver seguimientos');
    }

    public function view(User $user, FollowUp $followUp): bool
    {
        return $user->can('ver seguimientos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear seguimientos');
    }

    public function update(User $user, FollowUp $followUp): bool
    {
        return $user->can('editar seguimientos');
    }

    public function delete(User $user, FollowUp $followUp): bool
    {
        return $user->can('eliminar seguimientos');
    }

    public function restore(User $user, FollowUp $followUp): bool
    {
        return $user->can('eliminar seguimientos');
    }

    public function forceDelete(User $user, FollowUp $followUp): bool
    {
        return $user->can('eliminar seguimientos');
    }
}
