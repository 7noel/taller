<?php

namespace App\Policies;

use App\Models\Estimate;
use App\Models\User;

class EstimatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver presupuestos');
    }

    public function view(User $user, Estimate $estimate): bool
    {
        return $user->can('ver presupuestos');
    }

    public function create(User $user): bool
    {
        return $user->can('crear presupuestos');
    }

    public function update(User $user, Estimate $estimate): bool
    {
        return $user->can('editar presupuestos');
    }

    public function delete(User $user, Estimate $estimate): bool
    {
        return $user->can('eliminar presupuestos');
    }

    public function restore(User $user, Estimate $estimate): bool
    {
        return $user->can('eliminar presupuestos');
    }

    public function forceDelete(User $user, Estimate $estimate): bool
    {
        return $user->can('eliminar presupuestos');
    }

    public function sendToInsurance(User $user, Estimate $estimate): bool
    {
        return $user->can('editar presupuestos');
    }

    public function sendToClient(User $user, Estimate $estimate): bool
    {
        return $user->can('editar presupuestos');
    }

    public function approveInsurance(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function rejectInsurance(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function approveClient(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function rejectClient(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function startRepair(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function finalize(User $user, Estimate $estimate): bool
    {
        return $user->can('aprobar presupuestos');
    }

    public function returnToDraft(User $user, Estimate $estimate): bool
    {
        return $user->can('editar presupuestos');
    }
}