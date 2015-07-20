<?php 

	Route::resource('checkitem_groups','CheckitemGroupsController');
	Route::get('service_checklists/print/{id}', ['as' => 'autorepair.service_checklists.print', 'uses' => 'ServiceChecklistsController@print_out']);
	Route::resource('service_checklists','ServiceChecklistsController');