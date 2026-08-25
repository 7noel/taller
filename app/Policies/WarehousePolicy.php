<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver almacenes');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->can('ver almacenes');
    }

    public function create(User $user): bool
    {
        return $user->can('crear almacenes');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->can('editar almacenes');
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('eliminar almacenes');
    }

    public function restore(User $user, Warehouse $warehouse): bool
    {
        return $user->can('eliminar almacenes');
    }

    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('eliminar almacenes');
    }
}