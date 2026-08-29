<?php

namespace App\Policies;

use App\Models\ProviderSettlement;
use App\Models\User;

class ProviderSettlementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver liquidaciones de servicios');
    }

    public function view(User $user, ProviderSettlement $settlement): bool
    {
        return $user->can('ver liquidaciones de servicios');
    }

    public function create(User $user): bool
    {
        return $user->can('crear liquidaciones de servicios');
    }

    public function update(User $user, ProviderSettlement $settlement): bool
    {
        return $user->can('editar liquidaciones de servicios');
    }

    public function delete(User $user, ProviderSettlement $settlement): bool
    {
        return $user->can('eliminar liquidaciones de servicios');
    }
}
