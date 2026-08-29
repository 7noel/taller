<?php

namespace App\Policies;

use App\Models\CheckInChecklistItem;
use App\Models\User;

class CheckInChecklistItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver checklist');
    }

    public function view(User $user, CheckInChecklistItem $item): bool
    {
        return $user->can('ver checklist');
    }

    public function create(User $user): bool
    {
        return $user->can('crear checklist');
    }

    public function update(User $user, CheckInChecklistItem $item): bool
    {
        return $user->can('editar checklist');
    }

    public function delete(User $user, CheckInChecklistItem $item): bool
    {
        return $user->can('eliminar checklist');
    }

    public function restore(User $user, CheckInChecklistItem $item): bool
    {
        return $user->can('eliminar checklist');
    }

    public function forceDelete(User $user, CheckInChecklistItem $item): bool
    {
        return $user->can('eliminar checklist');
    }
}
