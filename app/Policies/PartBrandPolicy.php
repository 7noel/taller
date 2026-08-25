<?php

namespace App\Policies;

use App\Models\PartBrand;
use App\Models\User;

class PartBrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver marcas de repuesto');
    }

    public function view(User $user, PartBrand $brand): bool
    {
        return $user->can('ver marcas de repuesto');
    }

    public function create(User $user): bool
    {
        return $user->can('crear marcas de repuesto');
    }

    public function update(User $user, PartBrand $brand): bool
    {
        return $user->can('editar marcas de repuesto');
    }

    public function delete(User $user, PartBrand $brand): bool
    {
        return $user->can('eliminar marcas de repuesto');
    }
}