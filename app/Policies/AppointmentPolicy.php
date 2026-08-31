<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver citas');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->can('ver citas');
    }

    public function create(User $user): bool
    {
        return $user->can('crear citas');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->can('editar citas');
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('eliminar citas');
    }

    public function restore(User $user, Appointment $appointment): bool
    {
        return $user->can('eliminar citas');
    }

    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return $user->can('eliminar citas');
    }
}
