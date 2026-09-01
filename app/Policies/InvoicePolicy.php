<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver facturas');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('ver facturas');
    }

    public function create(User $user): bool
    {
        return $user->can('crear facturas');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('editar facturas') || $user->can('emitir comprobantes');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('anular facturas');
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->can('anular facturas');
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->can('anular facturas');
    }
}
