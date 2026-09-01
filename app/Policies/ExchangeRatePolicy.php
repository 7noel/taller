<?php

namespace App\Policies;

use App\Models\ExchangeRate;
use App\Models\User;

class ExchangeRatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver configuración');
    }

    public function create(User $user): bool
    {
        return $user->can('ver configuración');
    }

    public function delete(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('ver configuración');
    }
}
