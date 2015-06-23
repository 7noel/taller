<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function links()
    {
        $links = [
            'Seguridad'=>[
                ['name' => 'Usuarios', 'route' => 'guard.users.index' ],
                ['name' => 'Roles', 'route' => 'guard.roles.index', 'div' => '1' ],
                ['name' => 'Grupos', 'route' => 'guard.permission_groups.index' ],
                ['name' => 'Permisos', 'route' => 'guard.permissions.index' ],
            ],
            'Almacén'=>[
                ['name' => 'Almacenes', 'route' => 'storage.warehouses.index' ],
                ['name' => 'Productos', 'route' => 'storage.products.index' ],
                ['name' => 'Movimientos', 'route' => 'storage.products.index' ],
                ['name' => 'Categoías', 'route' => 'storage.categories.index', 'div' => '1' ],
                ['name' => 'Sub Categorías', 'route' => 'storage.sub_categories.index' ],
                ['name' => 'Tipos de Unidad', 'route' => 'admin.unit_types.index' ],
                ['name' => 'Unidades', 'route' => 'storage.units.index' ],
            ],
            'Ventas'=>[
                ['name' => 'Notas de Pedido de Vehículo', 'url' => '#' ],
                ['name' => 'Notas de Pedido de Accesorios', 'url' => '#' ],
                ['name' => 'Facturación', 'url' => '#' ],
                ['name' => 'Ordenes de Compra', 'url' => '#' ],
                ['name' => 'Cotización', 'route' => 'sales.car_quotes.index', 'div' => '1' ],
                ['name' => 'Catálogo de Vehículos', 'route' => 'sales.catalog_cars.index' ],
                ['name' => 'Versiones', 'route' => 'sales.versions.index' ],
                ['name' => 'Modelos', 'route' => 'sales.modelos.index' ],
                ['name' => 'Colores', 'route' => 'sales.colors.index' ],
                ['name' => 'Especificaciones', 'route' => 'sales.features.index' ],
                ['name' => 'Grupo de Especificaciones', 'route' => 'sales.feature_groups.index' ],
            ],
            'PostVenta'=>[
                ['name' => 'Ordenes de Trabajo', 'url' => '#' ],
                ['name' => 'Facturación', 'url' => '#' ],
                ['name' => 'Ordenes de Servicio', 'url' => '#' ],
                ['name' => 'Tipos de Ordenes', 'url' => '#', 'div' => '1' ],
            ],
            'Finanzas'=>[
                ['name' => 'Cuentas por Cobrar', 'url' => '#' ],
                ['name' => 'Cuentas por Pagar', 'url' => '#' ],
                ['name' => 'Facturación Total', 'url' => '#' ],
                ['name' => 'Monedas', 'route' => 'admin.currencies.index', 'div' => '1' ],
                ['name' => 'Tipo de Cambio', 'route' => 'finances.exchanges.index' ],
                ['name' => 'Empresas', 'route' => 'finances.companies.index' ],
                ['name' => 'Documentos', 'route' => 'admin.document_types.index' ],
                ['name' => 'Condiciones de Pago', 'route' => 'finances.payment_conditions.index' ],
            ],
            'Recursos Humanos'=>[
                ['name' => 'Empleados', 'route' => 'humanresources.employees.index' ],
                ['name' => 'Planilla', 'url' => '#' ],
                ['name' => 'Documentos', 'route' => 'admin.id_types.index' ],
            ],
            'Logística'=>[
                ['name' => 'Ordenes de Compra', 'url' => '#' ],
                ['name' => 'Compras', 'route' => 'logistics.purchases.index' ],
                ['name' => 'Marca', 'route' => 'logistics.brands.index', 'div' => '1' ],
            ],
        ];
        return $links;
    }
}
