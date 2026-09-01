<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver métodos de pago');
    }

    public function create(User $user): bool
    {
        return $user->can('crear métodos de pago');
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $user->can('eliminar métodos de pago');
    }
}
