<?php 

	Route::resource('modelos','ModelosController');
	Route::resource('versions','VersionsController');
	Route::resource('catalog_cars','CatalogCarsController');
	Route::resource('colors','ColorsController');
	Route::resource('features','FeaturesController');
	Route::resource('feature_groups','FeatureGroupsController');
	Route::get('car_quotes/print/{id}', ['as' => 'sales.car_quotes.print', 'uses' => 'CarQuotesController@print_out']);
	Route::resource('car_quotes','CarQuotesController');