<?php

namespace App\Policies;

use App\Models\InventoryGuide;
use App\Models\User;

class InventoryGuidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver guías de inventario');
    }

    public function view(User $user, InventoryGuide $guide): bool
    {
        return $user->can('ver guías de inventario');
    }

    public function create(User $user): bool
    {
        return $user->can('crear guías de inventario');
    }

    public function update(User $user, InventoryGuide $guide): bool
    {
        return $user->can('crear guías de inventario');
    }

    public function delete(User $user, InventoryGuide $guide): bool
    {
        return $user->can('eliminar guías de inventario');
    }
}
