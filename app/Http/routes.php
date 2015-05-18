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

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::group(['middleware'=>['auth']], function(){
	//Obtener provincas y distritos x ajax
	Route::get('listarProvincias/{departamento}', ['as' => 'ajaxprovincias', 'uses' => 'Admin\UbigeosController@ajaxProvincias']);
	Route::get('listarDistritos/{departamento}/{provincia}', ['as' => 'ajaxdistritos','uses' => 'Admin\UbigeosController@ajaxDistritos']);
	Route::get('listUnits/{unit_type_id}', ['as' => 'ajaxUnits','uses' => 'Storage\UnitsController@ajaxList']);
	Route::get('listSubCategories/{category_id}', ['as' => 'ajaxSubCategories','uses' => 'Storage\SubCategoriesController@ajaxList']);
	Route::get('listWarehouses', ['as' => 'ajaxWarehouses','uses' => 'Storage\WarehousesController@ajaxList']);
});

Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Admin'], function(){
	Route::resource('id_types','IdTypesController');
	Route::resource('unit_types','UnitTypesController');
	Route::resource('currencies','CurrenciesController');
});

Route::group(['prefix'=>'finances', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Finances'], function(){
	Route::resource('exchanges','ExchangesController');
	Route::resource('companies','CompaniesController');
});

/*Route::group(['prefix'=>'autorepair', 'middleware'=>['auth', 'permissions'], 'namespace'=>'AutoRepair'], function(){

});*/

Route::group(['prefix'=>'humanresources', 'middleware'=>['auth', 'permissions'], 'namespace'=>'HumanResources'], function(){
	Route::resource('employees','EmployeesController');
});

/*Route::group(['prefix'=>'logistics', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Logistics'], function(){

});*/

/*Route::group(['prefix'=>'sales', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Sales'], function(){

});*/
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