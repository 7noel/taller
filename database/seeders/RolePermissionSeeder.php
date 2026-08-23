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

        $roles = ['Administrador', 'Asesor', 'Técnico', 'Almacenero', 'Caja'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = [
            'ver parties', 'crear parties', 'editar parties', 'eliminar parties',
            'ver vehículos', 'crear vehículos', 'editar vehículos', 'eliminar vehículos',
            'ver marcas', 'crear marcas', 'editar marcas', 'eliminar marcas',
            'ver modelos', 'crear modelos', 'editar modelos', 'eliminar modelos',
            'ver inventarios', 'crear inventarios', 'editar inventarios', 'eliminar inventarios', 'aprobar inventarios',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::findByName('Administrador');
        $admin->givePermissionTo(Permission::all());

        $asesor = Role::findByName('Asesor');
        $asesor->givePermissionTo([
            'ver parties', 'crear parties', 'editar parties',
            'ver vehículos', 'crear vehículos', 'editar vehículos',
            'ver marcas', 'crear marcas', 'editar marcas',
            'ver modelos', 'crear modelos', 'editar modelos',
            'ver inventarios', 'crear inventarios', 'editar inventarios',
        ]);
    }
}