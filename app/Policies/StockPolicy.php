<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseStock;

class StockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver stock');
    }

    public function view(User $user, WarehouseStock $stock): bool
    {
        return $user->can('ver stock');
    }

    public function create(User $user): bool
    {
        return $user->can('crear movimientos');
    }
}