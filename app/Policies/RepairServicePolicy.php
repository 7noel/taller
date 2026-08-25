<?php

namespace App\Policies;

use App\Models\RepairService;
use App\Models\User;

class RepairServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver servicios');
    }

    public function view(User $user, RepairService $repairService): bool
    {
        return $user->can('ver servicios');
    }

    public function create(User $user): bool
    {
        return $user->can('crear servicios');
    }

    public function update(User $user, RepairService $repairService): bool
    {
        return $user->can('editar servicios');
    }

    public function delete(User $user, RepairService $repairService): bool
    {
        return $user->can('eliminar servicios');
    }

    public function restore(User $user, RepairService $repairService): bool
    {
        return $user->can('eliminar servicios');
    }

    public function forceDelete(User $user, RepairService $repairService): bool
    {
        return $user->can('eliminar servicios');
    }
}