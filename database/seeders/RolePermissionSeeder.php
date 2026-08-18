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
            Role::firstOrCreate(['name' => $role]);
        }

        // Permisos por módulo
        $permissions = [
            'ver parties', 'crear parties', 'editar parties', 'eliminar parties',
            'ver vehículos', 'crear vehículos', 'editar vehículos', 'eliminar vehículos',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Asignar todos los permisos a Administrador
        $admin = Role::findByName('Administrador');
        $admin->givePermissionTo(Permission::all());

        // Asesor: ver, crear y editar parties y vehículos
        $asesor = Role::findByName('Asesor');
        $asesor->givePermissionTo([
            'ver parties', 'crear parties', 'editar parties',
            'ver vehículos', 'crear vehículos', 'editar vehículos',
        ]);
    }
}