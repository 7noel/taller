<?php

namespace App\Policies;

use App\Models\CashRegister;
use App\Models\User;

class CashRegisterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver caja');
    }

    public function view(User $user, CashRegister $register): bool
    {
        return $user->can('ver caja');
    }

    public function create(User $user): bool
    {
        return $user->can('abrir caja') || $user->can('registrar movimientos de caja');
    }

    public function update(User $user, CashRegister $register): bool
    {
        return $user->can('cerrar caja');
    }

    public function delete(User $user, CashRegister $register): bool
    {
        return false;
    }

    public function restore(User $user, CashRegister $register): bool
    {
        return false;
    }

    public function forceDelete(User $user, CashRegister $register): bool
    {
        return false;
    }
}
