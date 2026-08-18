<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver marcas');
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->can('ver marcas');
    }

    public function create(User $user): bool
    {
        return $user->can('crear marcas');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->can('editar marcas');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('eliminar marcas');
    }
}