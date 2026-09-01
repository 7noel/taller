<?php

namespace App\Policies;

use App\Models\Dispatch;
use App\Models\User;

class DispatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver guías de remisión');
    }

    public function view(User $user, Dispatch $dispatch): bool
    {
        return $user->can('ver guías de remisión');
    }

    public function create(User $user): bool
    {
        return $user->can('crear guías de remisión');
    }

    public function update(User $user, Dispatch $dispatch): bool
    {
        return $user->can('editar guías de remisión');
    }

    public function delete(User $user, Dispatch $dispatch): bool
    {
        return $user->can('anular guías de remisión');
    }

    public function restore(User $user, Dispatch $dispatch): bool
    {
        return $user->can('anular guías de remisión');
    }

    public function forceDelete(User $user, Dispatch $dispatch): bool
    {
        return $user->can('anular guías de remisión');
    }
}
