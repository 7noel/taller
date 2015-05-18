<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Security\User;
use App\Modules\Security\Role;
use App\Modules\Security\Permission;
use App\Modules\Security\PermissionGroup;
use App\Modules\Base\IdType;
use App\Modules\Base\UnitType;
use App\Modules\Storage\Unit;
use App\Modules\Base\Currency;

use Faker\Factory as Faker;

class AdminTableSeeder extends Seeder {

    public function run()
    {
        //DB::table('users')->delete();

        User::create([
        	'name' => 'Noel',
        	'email' => 'noel.logan@gmail.com',
        	'password' => '123',
            'is_superuser' => true
        ]);

        for ($i=0; $i < 10; $i++) { 
	        $faker=Faker::create();
	        User::create([
	        	'name'=>$faker->firstName,
	        	'email'=>$faker->unique()->email,
	        	'password'=>'123'
			]);
        }
        Role::create(['name' => 'ADMINISTRADOR DE SISTEMA']);
        Role::create(['name' => 'JEFE DE ALMACEN']);
        Role::create(['name' => 'ASISTENTE DE ALMACEN']);
        Role::create(['name' => 'JEFE DE COMPRAS']);
        Role::create(['name' => 'VENDEDOR']);
        Role::create(['name' => 'GERENTE DE VENTAS']);
        Role::create(['name' => 'RECEPCIONISTA']);
        Role::create(['name' => 'ASESOR DE SERVICIO']);
        Role::create(['name' => 'COORDINADOR DE POSTVENTA']);
        Role::create(['name' => 'GERENTE DE POSTVENTA']);
        Role::create(['name' => 'JEFE DE TALLER']);
        Role::create(['name' => 'TECNICO']);
        Role::create(['name' => 'CAJERO']);
        Role::create(['name' => 'JEFE DE FINANZAS']);
        Role::create(['name' => 'ASISTENTE CONTABLE']);
        Role::create(['name' => 'CONTADOR GENERAL']);
        Role::create(['name' => 'ASISTENTE ADMINISTRATIVO']);
        Role::create(['name' => 'GERENTE GENERAL']);

        PermissionGroup::create(['name' => 'SISTEMAS']);
        PermissionGroup::create(['name' => 'ALMACEN']);
        PermissionGroup::create(['name' => 'LOGISTICA']);
        PermissionGroup::create(['name' => 'VENTAS']);
        PermissionGroup::create(['name' => 'POSTVENTA']);
        PermissionGroup::create(['name' => 'TALLER']);
        PermissionGroup::create(['name' => 'FINANZAS']);
        PermissionGroup::create(['name' => 'CONTABILIDAD']);
        PermissionGroup::create(['name' => 'ADMINISTRACION']);

        Permission::create(['name' => 'Usuarios Listar', 'action' => 'guard.users.index', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Usuarios Ver', 'action' => 'guard.users.show', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Usuarios Crear', 'action' => 'guard.users.create', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Usuarios Editar', 'action' => 'guard.users.edit', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Usuarios Eliminar', 'action' => 'guard.users.destroy', 'permission_group_id' => '1']);

        Permission::create(['name' => 'Roles Listar', 'action' => 'guard.roles.index', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Roles Ver', 'action' => 'guard.roles.show', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Roles Crear', 'action' => 'guard.roles.create', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Roles Editar', 'action' => 'guard.roles.edit', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Roles Eliminar', 'action' => 'guard.roles.destroy', 'permission_group_id' => '1']);

        Permission::create(['name' => 'Grupos Listar', 'action' => 'guard.permission_groups.index', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Grupos Ver', 'action' => 'guard.permission_groups.show', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Grupos Crear', 'action' => 'guard.permission_groups.create', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Grupos Editar', 'action' => 'guard.permission_groups.edit', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Grupos Eliminar', 'action' => 'guard.permission_groups.destroy', 'permission_group_id' => '1']);

        Permission::create(['name' => 'Permisos Listar', 'action' => 'guard.permissions.index', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Permisos Ver', 'action' => 'guard.permissions.show', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Permisos Crear', 'action' => 'guard.permissions.create', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Permisos Editar', 'action' => 'guard.permissions.edit', 'permission_group_id' => '1']);
        Permission::create(['name' => 'Permisos Eliminar', 'action' => 'guard.permissions.destroy', 'permission_group_id' => '1']);

        IdType::create(['name' => 'REGISTRO UNICO DE CONTRIBUYENTE', 'symbol' => 'RUC']);
        IdType::create(['name' => 'DOCUMENTO NACIONAL DE IDENTIDAD', 'symbol' => 'DNI']);
        IdType::create(['name' => 'CARNÉ DE EXTRANJERÍA', 'symbol' => 'CEX']);
        IdType::create(['name' => 'PASAPORTE', 'symbol' => 'PAS']);

        UnitType::create(['name' => 'LONGITUD']);
        UnitType::create(['name' => 'VOLUMEN']);
        UnitType::create(['name' => 'MASA']);

        Unit::create(['name' => 'CENTIMETRO', 'symbol' => 'cm', 'unit_type_id' => 1, 'value' => 1]);
        Unit::create(['name' => 'METRO', 'symbol' => 'mt', 'unit_type_id' => 1, 'value' => 100]);
        Unit::create(['name' => 'KILOMETRO', 'symbol' => 'km', 'unit_type_id' => 1, 'value' => 100000]);
        Unit::create(['name' => 'PULGADA', 'symbol' => 'pulg', 'unit_type_id' => 1, 'value' => 2.54]);
        Unit::create(['name' => 'PIE', 'symbol' => 'pie', 'unit_type_id' => 1, 'value' => 30.48]);
        Unit::create(['name' => 'YARDA', 'symbol' => 'yar', 'unit_type_id' => 1, 'value' => 91.44]);
        Unit::create(['name' => 'MILLA', 'symbol' => 'milla', 'unit_type_id' => 1, 'value' => 160934]);
        Unit::create(['name' => 'MILILITRO', 'symbol' => 'ml', 'unit_type_id' => 2, 'value' => 1]);
        Unit::create(['name' => 'LITRO', 'symbol' => 'lt', 'unit_type_id' => 2, 'value' => 1000]);
        Unit::create(['name' => 'METRO CUBICO', 'symbol' => 'm3', 'unit_type_id' => 2, 'value' => 1000000]);
        Unit::create(['name' => 'PULGADA CUBICA', 'symbol' => 'pulg3', 'unit_type_id' => 2, 'value' => 16.3871]);
        Unit::create(['name' => 'PIE CUBICO', 'symbol' => 'pie3', 'unit_type_id' => 2, 'value' => 28317]);
        Unit::create(['name' => 'GALON', 'symbol' => 'gal', 'unit_type_id' => 2, 'value' => 3785.4]);
        Unit::create(['name' => 'GRAMO', 'symbol' => 'gr', 'unit_type_id' => 3, 'value' => 1]);
        Unit::create(['name' => 'KILOGRAMO', 'symbol' => 'kg', 'unit_type_id' => 3, 'value' => 1000]);
        Unit::create(['name' => 'TONELADA', 'symbol' => 'ton', 'unit_type_id' => 3, 'value' => 1000000]);
        Unit::create(['name' => 'ONZA', 'symbol' => 'oz', 'unit_type_id' => 3, 'value' => 28.349]);
        Unit::create(['name' => 'LIBRA', 'symbol' => 'lb', 'unit_type_id' => 3, 'value' => 453.59]);

        Currency::create(['name' => 'NUEVOS SOLES', 'symbol' => 'S/.']);
        Currency::create(['name' => 'DOLARES AMERICANOS', 'symbol' => 'U$S']);

    }

}