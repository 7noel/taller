<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver bancos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear bancos');
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $user->can('eliminar bancos');
    }
}
