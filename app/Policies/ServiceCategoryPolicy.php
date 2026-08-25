<?php

namespace App\Policies;

use App\Models\ServiceCategory;
use App\Models\User;

class ServiceCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver categorías de servicio');
    }

    public function view(User $user, ServiceCategory $category): bool
    {
        return $user->can('ver categorías de servicio');
    }

    public function create(User $user): bool
    {
        return $user->can('crear categorías de servicio');
    }

    public function update(User $user, ServiceCategory $category): bool
    {
        return $user->can('editar categorías de servicio');
    }

    public function delete(User $user, ServiceCategory $category): bool
    {
        return $user->can('eliminar categorías de servicio');
    }
}