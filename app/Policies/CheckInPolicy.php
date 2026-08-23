<?php

namespace App\Policies;

use App\Models\CheckIn;
use App\Models\User;

class CheckInPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver inventarios');
    }

    public function view(User $user, CheckIn $checkIn): bool
    {
        return $user->can('ver inventarios');
    }

    public function create(User $user): bool
    {
        return $user->can('crear inventarios');
    }

    public function update(User $user, CheckIn $checkIn): bool
    {
        return $user->can('editar inventarios');
    }

    public function delete(User $user, CheckIn $checkIn): bool
    {
        return $user->can('eliminar inventarios');
    }

    public function restore(User $user, CheckIn $checkIn): bool
    {
        return $user->can('eliminar inventarios');
    }

    public function forceDelete(User $user, CheckIn $checkIn): bool
    {
        return $user->can('eliminar inventarios');
    }

    public function approve(User $user): bool
    {
        return $user->can('aprobar inventarios');
    }

    public function reject(User $user): bool
    {
        return $user->can('aprobar inventarios');
    }

    public function sendToClient(User $user): bool
    {
        return $user->can('editar inventarios');
    }

    public function uploadPhoto(User $user, CheckIn $checkIn): bool
    {
        return $user->can('editar inventarios');
    }

    public function deletePhoto(User $user, CheckIn $checkIn): bool
    {
        return $user->can('editar inventarios');
    }
}