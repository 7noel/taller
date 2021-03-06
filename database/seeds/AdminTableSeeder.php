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
use App\Modules\Finances\Exchange;
use App\Modules\Storage\Category;
use App\Modules\Storage\SubCategory;
use App\Modules\Storage\Warehouse;
use App\Modules\Logistics\Brand;
use App\Modules\Base\DocumentType;
use App\Modules\Finances\PaymentCondition;
use App\Modules\Sales\Modelo;
use App\Modules\Sales\Version;
use App\Modules\Sales\CatalogCar;
use App\Modules\Sales\Color;
use App\Modules\Sales\FeatureGroup;
use App\Modules\HumanResources\Job;

use Faker\Factory as Faker;

class AdminTableSeeder extends Seeder {

    public function run()
    {
        User::create([
        	'name' => 'Noel',
        	'email' => 'noel.logan@gmail.com',
        	'password' => '123',
            'is_superuser' => true
        ]);
        User::create([
            'name' => 'Juana',
            'email' => 'jlara@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Estuardo',
            'email' => 'etataje@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Marian',
            'email' => 'mderteano@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Pedro',
            'email' => 'prequena@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Pamela',
            'email' => 'pguerrero@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Eduardo',
            'email' => 'ecastellano@masaki.com.pe',
            'password' => '123'
        ]);
        User::create([
            'name' => 'Marco',
            'email' => 'mgeller@masaki.com.pe',
            'password' => '123',
        ]);
        User::create([
            'name' => 'Melissa',
            'email' => 'mmendoza@masaki.com.pe',
            'password' => '123'
        ]);
         User::create([
            'name' => 'Lung',
            'email' => 'alung@masaki.com.pe',
            'password' => '123',
            'is_superuser' => true
        ]);
        Role::create(['name' => 'ADMINISTRADOR DE SISTEMA']);
        Role::create(['name' => 'JEFE DE ALMACEN']);
        Role::create(['name' => 'ASISTENTE DE ALMACEN']);
        Role::create(['name' => 'JEFE DE COMPRAS']);
        Role::create(['name' => 'ASISTENTE DE ADV']);
        Role::create(['name' => 'ADMINISTRADOR DE VENTAS']);
        Role::create(['name' => 'VENDEDOR']);
        Role::create(['name' => 'JEFE DE VENTAS']);
        Role::create(['name' => 'RECEPCIONISTA']);
        Role::create(['name' => 'ASESOR DE SERVICIO']);
        Role::create(['name' => 'COORDINADOR DE POSTVENTA']);
        Role::create(['name' => 'JEFE DE POSTVENTA']);
        Role::create(['name' => 'JEFE DE TALLER']);
        Role::create(['name' => 'TECNICO']);
        Role::create(['name' => 'CAJERO']);
        Role::create(['name' => 'JEFE DE FINANZAS']);
        Role::create(['name' => 'ASISTENTE CONTABLE']);
        Role::create(['name' => 'CONTADOR GENERAL']);
        Role::create(['name' => 'ASISTENTE ADMINISTRATIVO']);
        Role::create(['name' => 'GERENTE GENERAL']);

        Job::create(['name' => 'ANALISTA DE SISTEMAS']);
        Job::create(['name' => 'ASESOR DE VENTAS']);
        Job::create(['name' => 'ADMINISTRADOR DE VENTAS']);
        Job::create(['name' => 'TECNICO']);
        Job::create(['name' => 'JEFE DE TALLER']);
        Job::create(['name' => 'ASESOR DE SERVICIO']);
        Job::create(['name' => 'COORDINADOR DE POSTVENTA']);
        Job::create(['name' => 'JEFE DE POSTVENTA']);

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

        Permission::create(['name' => 'Items Hoja Semáforo Listar', 'action' => 'autorepair.checkitem_groups.index', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Items Hoja Semáforo Ver', 'action' => 'autorepair.checkitem_groups.show', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Items Hoja Semáforo Crear', 'action' => 'autorepair.checkitem_groups.create', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Items Hoja Semáforo Editar', 'action' => 'autorepair.checkitem_groups.edit', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Items Hoja Semáforo Eliminar', 'action' => 'autorepair.checkitem_groups.destroy', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Listar', 'action' => 'autorepair.service_checklists.index', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Ver', 'action' => 'autorepair.service_checklists.show', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Crear', 'action' => 'autorepair.service_checklists.create', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Editar', 'action' => 'autorepair.service_checklists.edit', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Eliminar', 'action' => 'autorepair.service_checklists.destroy', 'permission_group_id' => '6']);
        Permission::create(['name' => 'Hoja Semáforo Imprimir', 'action' => 'autorepair.service_checklists.print', 'permission_group_id' => '6']);

        Permission::create(['name' => 'Colores Listar', 'action' => 'sales.colors.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Colores Ver', 'action' => 'sales.colors.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Colores Crear', 'action' => 'sales.colors.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Colores Editar', 'action' => 'sales.colors.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Colores Eliminar', 'action' => 'sales.colors.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Grupo de Especificaciones Listar', 'action' => 'sales.feature_groups.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Grupo de Especificaciones Ver', 'action' => 'sales.feature_groups.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Grupo de Especificaciones Crear', 'action' => 'sales.feature_groups.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Grupo de Especificaciones Editar', 'action' => 'sales.feature_groups.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Grupo de Especificaciones Eliminar', 'action' => 'sales.feature_groups.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Modelos Listar', 'action' => 'sales.modelos.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Modelos Ver', 'action' => 'sales.modelos.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Modelos Crear', 'action' => 'sales.modelos.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Modelos Editar', 'action' => 'sales.modelos.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Modelos Eliminar', 'action' => 'sales.modelos.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Versiones Listar', 'action' => 'sales.versions.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Versiones Ver', 'action' => 'sales.versions.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Versiones Crear', 'action' => 'sales.versions.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Versiones Editar', 'action' => 'sales.versions.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Versiones Eliminar', 'action' => 'sales.versions.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Catálogo de Vehículos Listar', 'action' => 'sales.catalog_cars.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Catálogo de Vehículos Ver', 'action' => 'sales.catalog_cars.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Catálogo de Vehículos Crear', 'action' => 'sales.catalog_cars.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Catálogo de Vehículos Editar', 'action' => 'sales.catalog_cars.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Catálogo de Vehículos Eliminar', 'action' => 'sales.catalog_cars.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Listar', 'action' => 'sales.car_quotes.index', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Ver', 'action' => 'sales.car_quotes.show', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Crear', 'action' => 'sales.car_quotes.create', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Editar', 'action' => 'sales.car_quotes.edit', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Eliminar', 'action' => 'sales.car_quotes.destroy', 'permission_group_id' => '4']);
        Permission::create(['name' => 'Cotización Imprimir', 'action' => 'sales.car_quotes.print', 'permission_group_id' => '4']);

        IdType::create(['name' => 'REGISTRO UNICO DE CONTRIBUYENTE', 'symbol' => 'RUC']);
        IdType::create(['name' => 'DOCUMENTO NACIONAL DE IDENTIDAD', 'symbol' => 'DNI']);
        IdType::create(['name' => 'CARNÉ DE EXTRANJERÍA', 'symbol' => 'CEX']);
        IdType::create(['name' => 'PASAPORTE', 'symbol' => 'PAS']);

        UnitType::create(['name' => 'LONGITUD']);
        UnitType::create(['name' => 'VOLUMEN']);
        UnitType::create(['name' => 'MASA']);
        UnitType::create(['name' => 'UNIDAD']);

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
        Unit::create(['name' => 'UNIDAD', 'symbol' => 'und', 'unit_type_id' => 4, 'value' => 1]);

        Currency::create(['name' => 'NUEVOS SOLES', 'symbol' => 'S/.']);
        Currency::create(['name' => 'DOLARES AMERICANOS', 'symbol' => 'US$']);

        Exchange::create(['date' => date('Y-m-d'), 'currency_id' => 1, 'sales' => 3, 'purchase' => 3]);

        Category::create(['name' => 'REPUESTOS']);

        SubCategory::create(['name' => 'ACCESORIOS', 'category_id' => 1]);
        SubCategory::create(['name' => 'ACEITES', 'category_id' => 1]);
        SubCategory::create(['name' => 'CARROCERIA', 'category_id' => 1]);
        SubCategory::create(['name' => 'CHASIS', 'category_id' => 1]);
        SubCategory::create(['name' => 'CUERPO / AIRE ACONDICIONADO', 'category_id' => 1]);//5
        SubCategory::create(['name' => 'DIRECCION', 'category_id' => 1]);
        SubCategory::create(['name' => 'ELECTRICIDAD', 'category_id' => 1]);
        SubCategory::create(['name' => 'ELECTRICO/ESCAPE/CALENT/SUMIN COMBU', 'category_id' => 1]);
        SubCategory::create(['name' => 'EMBRAGUE', 'category_id' => 1]);
        SubCategory::create(['name' => 'FLUIDO', 'category_id' => 1]);//10
        SubCategory::create(['name' => 'FRENOS', 'category_id' => 1]);
        SubCategory::create(['name' => 'INTERIOR / DEFENSAS', 'category_id' => 1]);
        SubCategory::create(['name' => 'LLANTAS', 'category_id' => 1]);
        SubCategory::create(['name' => 'LUBRICANTES', 'category_id' => 1]);
        SubCategory::create(['name' => 'MATERIALES', 'category_id' => 1]);//15
        SubCategory::create(['name' => 'MOTOR', 'category_id' => 1]);
        SubCategory::create(['name' => 'PEGAMENTO', 'category_id' => 1]);
        SubCategory::create(['name' => 'REFRIGERACION', 'category_id' => 1]);
        SubCategory::create(['name' => 'SUSPENSION', 'category_id' => 1]);
        SubCategory::create(['name' => 'TRANSMISION', 'category_id' => 1]);//20
        SubCategory::create(['name' => 'TRANSMISION- AUTOMATICO', 'category_id' => 1]);
        SubCategory::create(['name' => 'TRANSMISION-MANUAL', 'category_id' => 1]);
        SubCategory::create(['name' => 'VARIOS', 'category_id' => 1]);

        Warehouse::create(['name' => 'ALMACEN JAVIER PRADO', 'ubigeo_id' => 125114110, 'address' => 'AV.JAVIER PRADO ESTE 5446']);
        Warehouse::create(['name' => 'ALMACEN ATE', 'ubigeo_id' => 12441413, 'address' => 'AV. SEPARADORA INDUSTRIAL 781 ATE']);
        Warehouse::create(['name' => 'ALMACEN CUAS', 'ubigeo_id' => 125114110, 'address' => 'AV.JAVIER PRADO ESTE 5446']);
        Warehouse::create(['name' => 'LABORATORIO 1 LA MOLINA', 'ubigeo_id' => 125114110, 'address' => 'AV.JAVIER PRADO ESTE 5446']);
        Warehouse::create(['name' => 'LABORATORIO 2 ATE', 'ubigeo_id' => 12441413, 'address' => 'AV. SEPARADORA INDUSTRIAL 781 ATE']);
        Warehouse::create(['name' => 'JAD TRANSPORTES Y CONSTRUCCIONES SAC', 'ubigeo_id' => 18132411, 'address' => 'CALLE LAMBDA 205 CALLAO, ALTURA CUADRA 50 Y 51 DE LA COLONIAL']);
        Warehouse::create(['name' => 'ALMACEN ALTERNATIVO', 'ubigeo_id' => 125114110, 'address' => 'AV. SEPARADORA INDUSTRIAL 781 ATE']);

        Brand::create(['name' => 'NINGUNO', 'is_car' => '0']);
        Brand::create(['name' => 'HONDA', 'is_car' => '1']);
        Brand::create(['name' => '3M', 'is_car' => '0']);
        Brand::create(['name' => 'ABRO', 'is_car' => '0']);
        Brand::create(['name' => 'ALTERNATIVA', 'is_car' => '0']);//5
        Brand::create(['name' => 'BASF', 'is_car' => '0']);
        Brand::create(['name' => 'BOSCH', 'is_car' => '0']);
        Brand::create(['name' => 'CAPSA', 'is_car' => '0']);
        Brand::create(['name' => 'CHEVRON', 'is_car' => '0']);
        Brand::create(['name' => 'CONCEPT', 'is_car' => '0']);//10
        Brand::create(['name' => 'DURACELL', 'is_car' => '0']);
        Brand::create(['name' => 'ETNA', 'is_car' => '0']);
        Brand::create(['name' => 'FACTA', 'is_car' => '0']);
        Brand::create(['name' => 'FAST', 'is_car' => '0']);
        Brand::create(['name' => 'GARMIN', 'is_car' => '0']);//15
        Brand::create(['name' => 'GLASURIT', 'is_car' => '0']);
        Brand::create(['name' => 'GORILLA', 'is_car' => '0']);
        Brand::create(['name' => 'HELLA', 'is_car' => '0']);
        Brand::create(['name' => 'LG', 'is_car' => '0']);
        Brand::create(['name' => 'MAC', 'is_car' => '0']);//20
        Brand::create(['name' => 'MICHELIN', 'is_car' => '0']);
        Brand::create(['name' => 'MITSUBISHI', 'is_car' => '0']);
        Brand::create(['name' => 'NISSAN', 'is_car' => '0']);
        Brand::create(['name' => 'PRESTIGE', 'is_car' => '0']);
        Brand::create(['name' => 'PROTEMAX', 'is_car' => '0']);//25
        Brand::create(['name' => 'SHELL', 'is_car' => '0']);
        Brand::create(['name' => 'TOYOTA', 'is_car' => '0']);
        Brand::create(['name' => 'WURTH', 'is_car' => '0']);
        Brand::create(['name' => 'YOKOHAMA', 'is_car' => '0']);

        DocumentType::create(['name' => 'FACTURA', 'to_sales' => '1', 'to_purchases' => '1']);
        DocumentType::create(['name' => 'BOLETA', 'to_sales' => '1', 'to_purchases' => '1']);
        DocumentType::create(['name' => 'NOTA DE CREDITO', 'to_sales' => '1', 'to_purchases' => '1']);
        DocumentType::create(['name' => 'NOTA DE DEBITO', 'to_sales' => '1', 'to_purchases' => '1']);

        PaymentCondition::create(['name' => 'CONTADO', 'to_sales' => '1', 'to_purchases' => '1']);
        PaymentCondition::create(['name' => 'CRÉDITO', 'to_sales' => '1', 'to_purchases' => '1']);

        Modelo::create(['name' => 'FIT', 'brand_id' => '2', 'image' => 'logo_fit.png']);//1
        Modelo::create(['name' => 'CIVIC', 'brand_id' => '2', 'image' => 'logo_civic.png']);//2
        Modelo::create(['name' => 'CIVIC COUPE', 'brand_id' => '2', 'image' => 'logo_civic.png']);//3
        Modelo::create(['name' => 'ACCORD', 'brand_id' => '2', 'image' => 'logo_accord.png']);//4
        Modelo::create(['name' => 'ACCORD COUPE', 'brand_id' => '2', 'image' => 'logo_accord.png']);//5
        Modelo::create(['name' => 'CR-V', 'brand_id' => '2', 'image' => 'logo_crv.png']);//6
        Modelo::create(['name' => 'PILOT', 'brand_id' => '2', 'image' => 'logo_pilot.png']);//7
        Modelo::create(['name' => 'ODYSSEY', 'brand_id' => '2', 'image' => 'logo_odyssey.png']);//8
        //FIT
        Version::create(['name' => 'LX MT', 'modelo_id' => '1']);
        Version::create(['name' => 'LX AT', 'modelo_id' => '1']);
        Version::create(['name' => 'EX AT', 'modelo_id' => '1']);
        //CIVIC SEDAN
        Version::create(['name' => 'LX AT', 'modelo_id' => '2']);
        Version::create(['name' => 'DE LUXE', 'modelo_id' => '2']);
        Version::create(['name' => 'SI 2.4 MT', 'modelo_id' => '2']);
        //CIVIC COUPE
        Version::create(['name' => 'EX AT', 'modelo_id' => '3']);
        Version::create(['name' => 'SI 2.4 MT', 'modelo_id' => '3']);
        //ACCORD SEDAN
        Version::create(['name' => 'EX AT', 'modelo_id' => '4']);
        Version::create(['name' => 'EXL AT', 'modelo_id' => '4']);
        Version::create(['name' => 'V6 AT', 'modelo_id' => '4']);
        //ACCORD COUPE
        Version::create(['name' => 'V6 AT', 'modelo_id' => '5']);
        //CR-V
        Version::create(['name' => 'LX CVT 2WD Up-Grade', 'modelo_id' => '6']);
        Version::create(['name' => 'LX CVT 2WD Up-Grade', 'modelo_id' => '6']);
        Version::create(['name' => 'DE LUXE', 'modelo_id' => '6']);
        Version::create(['name' => 'TOURING', 'modelo_id' => '6']);
        //PILOT
        Version::create(['name' => 'LX Up-Grade', 'modelo_id' => '7']);
        Version::create(['name' => 'EXL Up-Grade', 'modelo_id' => '7']);
        Version::create(['name' => 'TOURING', 'modelo_id' => '7']);
        //ODYSSEY
        Version::create(['name' => 'EXL', 'modelo_id' => '8']);

        FeatureGroup::create(['name' => 'Especificaciones Técnicas Principales :', 'template' => 'primaryLeft']);
        FeatureGroup::create(['name' => 'Dimensiones :', 'template' => 'primaryRight']);
        FeatureGroup::create(['name' => 'CONFORT Y TECNOLOGIA :', 'template' => 'in']);
        FeatureGroup::create(['name' => 'SISTEMA DE SEGURIDAD :', 'template' => 'out']);

        Color::create(['name' => 'NEGRO', 'in' => '1', 'out' => '1']);
        Color::create(['name' => 'AZUL', 'in' => '1', 'out' => '1']);
        Color::create(['name' => 'BEIGE', 'in' => '1', 'out' => '1']);
        
    }
}