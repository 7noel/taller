<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Crear roles
        $roles = ['Administrador', 'Asesor', 'Técnico', 'Almacenero', 'Caja'];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
        // Permisos básicos por módulo (ejemplo para clientes)
        $permissions = [
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver vehículos', 'crear vehículos', 'editar vehículos', 'eliminar vehículos',
            // Agregar más según se necesite
        ];
        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }
        // Asignar todos los permisos a Administrador
        $admin = Role::findByName('Administrador');
        $admin->givePermissionTo(Permission::all());
    }
}