<?php 

	//Obtener provincas y distritos x ajax
	Route::get('listarProvincias/{departamento}', ['as' => 'ajaxprovincias', 'uses' => 'Admin\UbigeosController@ajaxProvincias']);
	Route::get('listarDistritos/{departamento}/{provincia}', ['as' => 'ajaxdistritos','uses' => 'Admin\UbigeosController@ajaxDistritos']);
	//Obtiene lista de unidades de medida segun el tipo seleccionado x ajax
	Route::get('listUnits/{unit_type_id}', ['as' => 'ajaxUnits','uses' => 'Storage\UnitsController@ajaxList']);
	//Obtiene lista de subcategorias de productos segun la categoria seleccionada x ajax
	Route::get('listSubCategories/{category_id}', ['as' => 'ajaxSubCategories','uses' => 'Storage\SubCategoriesController@ajaxList']);
	//Obtiene lista de almacenes x ajax
	Route::get('listWarehouses', ['as' => 'ajaxWarehouses','uses' => 'Storage\WarehousesController@ajaxList']);
	//Obtiene lista de empresas x ajax
	Route::get('finances/companies/autocomplete', ['as' => 'companiesAutocomplete','uses' => 'Finances\CompaniesController@ajaxAutocomplete']);
	//Obtiene lista de productos segun el almacén seleccionado x ajax
	Route::get('storage/products/autocomplete/{warehouse_id}', ['as' => 'productsAutocomplete','uses' => 'Storage\ProductsController@ajaxAutocomplete']);
	//Obtiene data del producto según producto y almacen seleccionado x ajax
	Route::get('storage/products/ajaxGetData/{warehouse_id}/{product_id}', ['as' => 'ajaxGetData','uses' => 'Storage\ProductsController@ajaxGetData']);
	//Obtiene lista de vehiculos (año_fab/año_model) segun la version seleccionada x ajax
	Route::get('listCars/{version_id}', ['as' => 'ajaxCars','uses' => 'Sales\CatalogCarsController@ajaxList']);
	//Obtiene data del vehiculo segun el vehiculo seleccionado x ajax
	Route::get('ajaxGetCar/{catalog_car_id}', ['as' => 'ajaxGetCar','uses' => 'Sales\CatalogCarsController@ajaxGetCar']);
	//Obtiene lista de usuarios x ajax
	Route::get('guard/users/autocomplete', ['as' => 'usersAutocomplete','uses' => 'Security\UsersController@ajaxAutocomplete']);
	//Obtiene data de la OT segun la OT seleccionada x ajax
	Route::get('autorepair/service_checklists/ajaxGetOt/{order_id}', ['as' => 'ajaxGetOt','uses' => 'AutoRepair\ServiceChecklistsController@ajaxGetOt']);
 ?>