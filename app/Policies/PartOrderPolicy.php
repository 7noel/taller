<?php

namespace App\Policies;

use App\Models\PartOrder;
use App\Models\User;

class PartOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver pedidos de repuestos');
    }

    public function view(User $user, PartOrder $order): bool
    {
        return $user->can('ver pedidos de repuestos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear pedidos de repuestos');
    }

    public function update(User $user, PartOrder $order): bool
    {
        return $user->can('editar pedidos de repuestos');
    }

    public function delete(User $user, PartOrder $order): bool
    {
        return $user->can('eliminar pedidos de repuestos');
    }
}
