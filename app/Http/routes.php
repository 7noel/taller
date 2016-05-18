<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', 'WelcomeController@index');
Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::group(['middleware'=>['auth']], function(){
	//Obtener provincas y distritos x ajax
	Route::get('listarProvincias/{departamento}', ['as' => 'ajaxprovincias', 'uses' => 'Admin\UbigeosController@ajaxProvincias']);
	Route::get('listarDistritos/{departamento}/{provincia}', ['as' => 'ajaxdistritos','uses' => 'Admin\UbigeosController@ajaxDistritos']);
	Route::get('listarDistritos2/{departamento}/{provincia}', ['as' => 'ajaxdistritos','uses' => 'Admin\UbigeosController@ajaxDistritos2']);
	Route::get('listUnits/{unit_type_id}', ['as' => 'ajaxUnits','uses' => 'Storage\UnitsController@ajaxList']);
	Route::get('listSubCategories/{category_id}', ['as' => 'ajaxSubCategories','uses' => 'Storage\SubCategoriesController@ajaxList']);
	Route::get('listWarehouses', ['as' => 'ajaxWarehouses','uses' => 'Storage\WarehousesController@ajaxList']);
	Route::get('finances/companies/autocomplete', ['as' => 'companiesAutocomplete','uses' => 'Finances\CompaniesController@ajaxAutocomplete']);
	Route::get('storage/products/autocomplete/{warehouse_id}', ['as' => 'productsAutocomplete','uses' => 'Storage\ProductsController@ajaxAutocomplete']);
	Route::get('storage/products/ajaxGetData/{warehouse_id}/{product_id}', ['as' => 'ajaxGetData','uses' => 'Storage\ProductsController@ajaxGetData']);
	Route::get('listCars/{version_id}', ['as' => 'ajaxCars','uses' => 'Sales\CatalogCarsController@ajaxList']);
	Route::get('ajaxGetCar/{catalog_car_id}', ['as' => 'ajaxGetCar','uses' => 'Sales\CatalogCarsController@ajaxGetCar']);
	Route::get('guard/users/autocomplete', ['as' => 'usersAutocomplete','uses' => 'Security\UsersController@ajaxAutocomplete']);
	Route::get('autorepair/service_checklists/ajaxGetOt/{order_id}', ['as' => 'ajaxGetOt','uses' => 'AutoRepair\ServiceChecklistsController@ajaxGetOt']);

	Route::get('autorepair/ajaxGetAsesorByOrder/{orderasesor}/{date}', ['as' => 'ajaxGetAsesorByOrder', 'uses' => 'Autorepair\AdviserSchedulesController@ajaxGetAsesorByOrder'] );
});

Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Admin'], function(){
	Route::resource('id_types','IdTypesController');
	Route::resource('unit_types','UnitTypesController');
	Route::resource('currencies','CurrenciesController');
	Route::resource('document_types','DocumentTypesController');
});

Route::group(['prefix'=>'finances', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Finances'], function(){
	Route::resource('exchanges','ExchangesController');
	Route::resource('companies','CompaniesController');
	Route::resource('payment_conditions','PaymentConditionsController');
});

Route::group(['prefix'=>'autorepair', 'middleware'=>['auth', 'permissions'], 'namespace'=>'AutoRepair'], function(){
	Route::resource('checkitem_groups','CheckitemGroupsController');
	Route::resource('appointments','AppointmentsController');
	Route::resource('adviser_schedules','AdviserSchedulesController');
	Route::get('service_checklists/print/{id}', ['as' => 'autorepair.service_checklists.print', 'uses' => 'ServiceChecklistsController@print_out']);

	Route::get('service_checklists/change_status/{id}', ['as' => 'autorepair.service_checklists.change_status', 'uses' => 'ServiceChecklistsController@editStatus'] );
	Route::get('service_checklists/save_status/{id}', ['as' => 'autorepair.service_checklists.save_status', 'uses' => 'ServiceChecklistsController@updateStatus'] );
	Route::get('service_reminder/change_status/{plate}', ['as' => 'autorepair.service_reminder.change_status', 'uses' => 'ServiceChecklistsController@editStatusReminder'] );
	Route::get('service_reminder/save_status/{plate}', ['as' => 'autorepair.service_reminder.save_status', 'uses' => 'ServiceChecklistsController@updateStatusReminder'] );
	Route::resource('service_checklists','ServiceChecklistsController');
	Route::get('vehicles/nextservice', ['as' => 'autorepair.vehicles.nextservice', 'uses' => 'ServiceChecklistsController@nextservice']);
	Route::post('service_reminder/emailconfirm/{plate}', ['as' => 'autorepair.service_reminder.send_email', 'uses' => 'ServiceChecklistsController@sendEmail'] );
	Route::get('service_reminder/emailform/{plate}', ['as' => 'autorepair.service_reminder.form_email', 'uses' => 'ServiceChecklistsController@formEmail'] );

	Route::get('schedulings/ajaxGetTime/{date}', ['as' => 'autorepair.schedulings.ajaxGetTime', 'uses' => 'SchedulingsController@ajaxGetTime'] );
	Route::get('schedulings/ajaxGetAsesores/{date}/{time}', ['as' => 'autorepair.schedulings.ajaxGetAsesores', 'uses' => 'SchedulingsController@ajaxGetAsesores'] );
	Route::get('vehiculos/ajaxGetData/{plate}', ['as' => 'autorepair.schedulings.ajaxGetClienteByPlaca', 'uses' => 'SchedulingsController@ajaxGetClienteByPlate'] );
});

Route::group(['prefix'=>'humanresources', 'middleware'=>['auth', 'permissions'], 'namespace'=>'HumanResources'], function(){
	Route::resource('employees','EmployeesController');
	Route::resource('jobs','JobsController');
});

Route::group(['prefix'=>'logistics', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Logistics'], function(){
	Route::resource('brands','BrandsController');
	Route::resource('purchases','PurchasesController');
});

Route::group(['prefix'=>'sales', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Sales'], function(){
	Route::resource('modelos','ModelosController');
	Route::resource('versions','VersionsController');
	Route::resource('catalog_cars','CatalogCarsController');
	Route::resource('colors','ColorsController');
	Route::resource('features','FeaturesController');
	Route::resource('feature_groups','FeatureGroupsController');
	Route::get('afluencia/crear', ['as' => 'sales.afluencia.create', 'uses' => 'CarQuotesController@afluencia_form']);
	Route::get('afluencia/', ['as' => 'sales.car_quotes.afluencia.index', 'uses' => 'CarQuotesController@afluencia']);
	Route::get('car_quotes/index/{company_id}', ['as' => 'sales.car_quotes.company_id', 'uses' => 'CarQuotesController@byCompany']);
	Route::get('car_quotes/print/{id}', ['as' => 'sales.car_quotes.print', 'uses' => 'CarQuotesController@print_out']);
	Route::resource('car_quotes','CarQuotesController');
	Route::resource('clientes','ClientesController');
});
Route::group(['prefix'=>'storage', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Storage'], function(){
	Route::resource('units','UnitsController');
	Route::resource('warehouses','WarehousesController');
	Route::resource('categories','CategoriesController');
	Route::resource('sub_categories','SubCategoriesController');
	Route::resource('products','ProductsController');
});
Route::group(['prefix'=>'guard', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Security'], function(){
	Route::get('change_password', ['as' => 'change_password', 'uses' => 'UsersController@changePassword']);
	Route::post('update_password', ['as'=>'update_password', 'uses'=>'UsersController@updatePassword']);
	Route::resource('users','UsersController');
	Route::resource('roles','RolesController');
	Route::resource('permissions','PermissionsController');
	Route::resource('permission_groups','PermissionGroupsController');
});