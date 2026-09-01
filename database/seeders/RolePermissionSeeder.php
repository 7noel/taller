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

        $roles = ['Administrador', 'Asesor', 'Técnico', 'Almacenero', 'Caja', 'Gestor de Citas'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = [
            'ver parties', 'crear parties', 'editar parties', 'eliminar parties',
            'ver vehículos', 'crear vehículos', 'editar vehículos', 'eliminar vehículos',
            'ver marcas', 'crear marcas', 'editar marcas', 'eliminar marcas',
            'ver modelos', 'crear modelos', 'editar modelos', 'eliminar modelos',
            'ver inventarios', 'crear inventarios', 'editar inventarios', 'eliminar inventarios', 'aprobar inventarios',
            'ver presupuestos', 'crear presupuestos', 'editar presupuestos', 'eliminar presupuestos', 'aprobar presupuestos',
            'ver órdenes de trabajo', 'crear órdenes de trabajo', 'editar órdenes de trabajo', 'eliminar órdenes de trabajo',
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            'ver roles', 'crear roles', 'editar roles', 'eliminar roles',
            'ver permisos',
            'ver configuración', 'editar configuración',
            'ver establecimientos', 'crear establecimientos', 'editar establecimientos', 'eliminar establecimientos',
            'ver series', 'crear series', 'editar series', 'eliminar series',
            'ver servicios', 'crear servicios', 'editar servicios', 'eliminar servicios',
            'ver repuestos', 'crear repuestos', 'editar repuestos', 'eliminar repuestos',
            'ver almacenes', 'crear almacenes', 'editar almacenes', 'eliminar almacenes',
            'ver stock', 'crear movimientos',
            'ver categorías de servicio', 'crear categorías de servicio', 'editar categorías de servicio', 'eliminar categorías de servicio',
            'ver categorías de repuesto', 'crear categorías de repuesto', 'editar categorías de repuesto', 'eliminar categorías de repuesto',
            'ver marcas de repuesto', 'crear marcas de repuesto', 'editar marcas de repuesto', 'eliminar marcas de repuesto',
            'ver plantillas de formulario', 'crear plantillas de formulario', 'editar plantillas de formulario', 'eliminar plantillas de formulario',
            'ver checklist', 'crear checklist', 'editar checklist', 'eliminar checklist',
            'ver tablero',
            'ver vales de servicio', 'crear vales de servicio', 'editar vales de servicio', 'eliminar vales de servicio',
            'ver liquidaciones de servicios', 'crear liquidaciones de servicios', 'editar liquidaciones de servicios', 'eliminar liquidaciones de servicios',
            'ver órdenes de compra', 'crear órdenes de compra', 'editar órdenes de compra', 'eliminar órdenes de compra', 'recibir órdenes de compra',
            'ver guías de inventario', 'crear guías de inventario', 'eliminar guías de inventario',
            'ver pedidos de repuestos', 'crear pedidos de repuestos', 'editar pedidos de repuestos', 'eliminar pedidos de repuestos',
            'ver citas', 'crear citas', 'editar citas', 'eliminar citas',
            'ver seguimientos', 'crear seguimientos', 'editar seguimientos', 'eliminar seguimientos',
            'ver facturas', 'crear facturas', 'editar facturas', 'emitir comprobantes', 'anular facturas',
            'ver guías de remisión', 'crear guías de remisión', 'editar guías de remisión', 'anular guías de remisión',
            'ver caja', 'abrir caja', 'cerrar caja', 'registrar movimientos de caja',
            'ver métodos de pago', 'crear métodos de pago', 'eliminar métodos de pago',
            'ver bancos', 'crear bancos', 'eliminar bancos',
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
            'ver presupuestos', 'crear presupuestos', 'editar presupuestos', 'aprobar presupuestos',
            'ver órdenes de trabajo', 'crear órdenes de trabajo', 'editar órdenes de trabajo',
            'ver vales de servicio', 'crear vales de servicio', 'editar vales de servicio',
            'ver liquidaciones de servicios', 'crear liquidaciones de servicios', 'editar liquidaciones de servicios',
            'ver usuarios',
            'ver servicios', 'crear servicios', 'editar servicios',
            'ver repuestos', 'crear repuestos', 'editar repuestos',
            'ver almacenes', 'crear almacenes', 'editar almacenes',
            'ver stock', 'crear movimientos',
            'ver pedidos de repuestos',

            'ver categorías de servicio', 'crear categorías de servicio', 'editar categorías de servicio',
            'ver categorías de repuesto', 'crear categorías de repuesto', 'editar categorías de repuesto',
            'ver marcas de repuesto', 'crear marcas de repuesto', 'editar marcas de repuesto',
            'ver plantillas de formulario',
            'ver checklist',
            'ver tablero',
            'ver citas', 'crear citas', 'editar citas',
            'ver seguimientos', 'crear seguimientos', 'editar seguimientos',
            'ver facturas', 'crear facturas', 'editar facturas', 'emitir comprobantes',
            'ver guías de remisión', 'crear guías de remisión', 'editar guías de remisión',
            'ver caja', 'abrir caja', 'cerrar caja', 'registrar movimientos de caja',
            'ver métodos de pago', 'crear métodos de pago',
            'ver bancos', 'crear bancos',
        ]);

        // Almacenero gestiona repuestos/stock
        $almacenero = Role::findByName('Almacenero');
        $almacenero->givePermissionTo([
            'ver repuestos', 'crear repuestos', 'editar repuestos',
            'ver almacenes',
            'ver stock', 'crear movimientos',
            'ver órdenes de compra', 'crear órdenes de compra', 'editar órdenes de compra', 'recibir órdenes de compra',
            'ver guías de inventario', 'crear guías de inventario',
            'ver pedidos de repuestos', 'crear pedidos de repuestos', 'editar pedidos de repuestos',
            'ver categorías de repuesto', 'crear categorías de repuesto', 'editar categorías de repuesto',
            'ver marcas de repuesto', 'crear marcas de repuesto', 'editar marcas de repuesto',
        ]);

        // El técnico consulta y ejecuta las órdenes de trabajo
        $tecnico = Role::findByName('Técnico');
        $tecnico->givePermissionTo([
            'ver órdenes de trabajo',
        ]);

        // Gestor de Citas: agenda, recordatorios y seguimientos + presupuestos directos
        // (creados sin inventario vehicular, para convertir prospectos en ventas).
        $gestor = Role::findByName('Gestor de Citas');
        $gestor->givePermissionTo([
            'ver parties',
            'ver vehículos',
            'ver presupuestos', 'crear presupuestos',
            'ver citas', 'crear citas', 'editar citas',
            'ver seguimientos', 'crear seguimientos', 'editar seguimientos',
        ]);
    }
}