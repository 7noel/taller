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
	//cambiar contraseña
	Route::get('change_password', ['as' => 'change_password', 'uses' => 'Security\UsersController@changePassword']);
	Route::post('update_password', ['as' => 'update_password', 'uses'=>'Security\UsersController@updatePassword']);
	//metodos ajax
	require(__DIR__.'/routes/ajax.php');
});

Route::group(['prefix'=>'admin', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Admin'], function(){
	require(__DIR__.'/routes/admin.php');
});

Route::group(['prefix'=>'finances', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Finances'], function(){
	require(__DIR__.'/routes/finances.php');
});

Route::group(['prefix'=>'autorepair', 'middleware'=>['auth', 'permissions'], 'namespace'=>'AutoRepair'], function(){
	require(__DIR__.'/routes/autorepair.php');
});

Route::group(['prefix'=>'humanresources', 'middleware'=>['auth', 'permissions'], 'namespace'=>'HumanResources'], function(){
	require(__DIR__.'/routes/humanresources.php');
});

Route::group(['prefix'=>'logistics', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Logistics'], function(){
	require(__DIR__.'/routes/logistics.php');
});

Route::group(['prefix'=>'sales', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Sales'], function(){
	require(__DIR__.'/routes/sales.php');
});
Route::group(['prefix'=>'storage', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Storage'], function(){
	require(__DIR__.'/routes/storage.php');
});
Route::group(['prefix'=>'guard', 'middleware'=>['auth', 'permissions'], 'namespace'=>'Security'], function(){
	require(__DIR__.'/routes/guard.php');
});