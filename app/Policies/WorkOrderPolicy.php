<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver órdenes de trabajo');
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('ver órdenes de trabajo');
    }

    public function create(User $user): bool
    {
        return $user->can('crear órdenes de trabajo');
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('editar órdenes de trabajo');
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('eliminar órdenes de trabajo');
    }

    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('eliminar órdenes de trabajo');
    }

    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('eliminar órdenes de trabajo');
    }

    public function changeStatus(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('editar órdenes de trabajo');
    }

    public function attachEstimate(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('editar órdenes de trabajo');
    }

    public function manageAssignments(User $user, WorkOrder $workOrder): bool
    {
        return $user->can('editar órdenes de trabajo');
    }
}
