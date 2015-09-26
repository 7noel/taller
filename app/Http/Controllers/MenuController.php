<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\Security\UserRepo;

class MenuController extends Controller
{
    protected $repo;

    public function __construct(UserRepo $repo) {
        $this->repo = $repo;
    }
    public function links()
    {
        $arrayLinks = $this->arrayLinks();

        if (\Auth::user()->is_superuser == true) {
            return $arrayLinks;
        }

        $permissions = $this->repo->allPermissions();

        foreach ($arrayLinks as $k => $module) {
            foreach ($module as $key => $link) {
                if (!isset($link['route'])) {
                    unset($arrayLinks[$k][$key]);
                } else if (!in_array($link['route'], $permissions)) {
                    unset($arrayLinks[$k][$key]);
                }
            }
            if (count($arrayLinks[$k]) == 0) {
                unset($arrayLinks[$k]);
            }
        }

        return $arrayLinks;

    }
    public function arrayLinks()
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
                ['name' => 'Cotización', 'route' => 'sales.car_quotes.index'],
                ['name' => 'Catálogo de Vehículos', 'route' => 'sales.catalog_cars.index' ],
                ['name' => 'Notas de Pedido de Vehículo', 'url' => '#' ],
                ['name' => 'Notas de Pedido de Accesorios', 'url' => '#' ],
                ['name' => 'Facturación', 'url' => '#' ],
                ['name' => 'Ordenes de Compra', 'url' => '#' ],
                ['name' => 'Versiones', 'route' => 'sales.versions.index', 'div' => '1'],
                ['name' => 'Modelos', 'route' => 'sales.modelos.index' ],
                ['name' => 'Colores', 'route' => 'sales.colors.index' ],
                ['name' => 'Grupo de Especificaciones', 'route' => 'sales.feature_groups.index' ],
            ],
            'PostVenta'=>[
                ['name' => 'Hoja Semaforo', 'route' => 'autorepair.service_checklists.index' ],
                ['name' => 'Items de Hoja Semaforo', 'route' => 'autorepair.checkitem_groups.index' ],
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
                ['name' => 'Cargos', 'route' => 'humanresources.jobs.index' ],
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