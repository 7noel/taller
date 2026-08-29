<?php

namespace App\Policies;

use App\Models\ServiceVoucher;
use App\Models\User;

class ServiceVoucherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver vales de servicio');
    }

    public function view(User $user, ServiceVoucher $voucher): bool
    {
        return $user->can('ver vales de servicio');
    }

    public function create(User $user): bool
    {
        return $user->can('crear vales de servicio');
    }

    public function update(User $user, ServiceVoucher $voucher): bool
    {
        return $user->can('editar vales de servicio');
    }

    public function delete(User $user, ServiceVoucher $voucher): bool
    {
        return $user->can('eliminar vales de servicio');
    }
}
