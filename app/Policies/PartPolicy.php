<?php

namespace App\Policies;

use App\Models\Part;
use App\Models\User;

class PartPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver repuestos');
    }

    public function view(User $user, Part $part): bool
    {
        return $user->can('ver repuestos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear repuestos');
    }

    public function update(User $user, Part $part): bool
    {
        return $user->can('editar repuestos');
    }

    public function delete(User $user, Part $part): bool
    {
        return $user->can('eliminar repuestos');
    }

    public function restore(User $user, Part $part): bool
    {
        return $user->can('eliminar repuestos');
    }

    public function forceDelete(User $user, Part $part): bool
    {
        return $user->can('eliminar repuestos');
    }
}