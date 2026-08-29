<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver órdenes de compra');
    }

    public function view(User $user, PurchaseOrder $po): bool
    {
        return $user->can('ver órdenes de compra');
    }

    public function create(User $user): bool
    {
        return $user->can('crear órdenes de compra');
    }

    public function update(User $user, PurchaseOrder $po): bool
    {
        return $user->can('editar órdenes de compra');
    }

    public function delete(User $user, PurchaseOrder $po): bool
    {
        return $user->can('eliminar órdenes de compra');
    }

    public function receive(User $user, PurchaseOrder $po): bool
    {
        return $user->can('recibir órdenes de compra');
    }
}
