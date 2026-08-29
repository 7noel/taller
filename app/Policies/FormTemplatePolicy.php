<?php

namespace App\Policies;

use App\Models\FormTemplate;
use App\Models\User;

class FormTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ver plantillas de formulario');
    }

    public function view(User $user, FormTemplate $formTemplate): bool
    {
        return $user->can('ver plantillas de formulario');
    }

    public function create(User $user): bool
    {
        return $user->can('crear plantillas de formulario');
    }

    public function update(User $user, FormTemplate $formTemplate): bool
    {
        return $user->can('editar plantillas de formulario');
    }

    public function delete(User $user, FormTemplate $formTemplate): bool
    {
        return $user->can('eliminar plantillas de formulario');
    }

    public function restore(User $user, FormTemplate $formTemplate): bool
    {
        return $user->can('eliminar plantillas de formulario');
    }

    public function forceDelete(User $user, FormTemplate $formTemplate): bool
    {
        return $user->can('eliminar plantillas de formulario');
    }
}
