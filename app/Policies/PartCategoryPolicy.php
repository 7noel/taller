<?php

namespace App\Policies;

use App\Models\PartCategory;
use App\Models\User;

class PartCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver categorías de repuesto');
    }

    public function view(User $user, PartCategory $category): bool
    {
        return $user->can('ver categorías de repuesto');
    }

    public function create(User $user): bool
    {
        return $user->can('crear categorías de repuesto');
    }

    public function update(User $user, PartCategory $category): bool
    {
        return $user->can('editar categorías de repuesto');
    }

    public function delete(User $user, PartCategory $category): bool
    {
        return $user->can('eliminar categorías de repuesto');
    }
}