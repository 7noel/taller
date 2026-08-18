<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleModel;

class VehicleModelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver modelos');
    }

    public function view(User $user, VehicleModel $model): bool
    {
        return $user->can('ver modelos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear modelos');
    }

    public function update(User $user, VehicleModel $model): bool
    {
        return $user->can('editar modelos');
    }

    public function delete(User $user, VehicleModel $model): bool
    {
        return $user->can('eliminar modelos');
    }
}