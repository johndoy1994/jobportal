<?php

Route::group(['prefix'=>'crud'], function() {
	// CRUD Example
	// Listing
	Route::get('/', ['uses'=>"CRUDExampleController@getListing", 'as'=>"admin-crud"]);

	// New Item
	Route::get('/new-item', ['uses'=>"CRUDExampleController@getNewItem", 'as'=>"admin-crud-new-item"]);
	Route::post('/new-item', ['uses'=>"CRUDExampleController@postNewItem", 'as'=>"admin-crud-new-item-post"]);

	// Edit Item
	Route::get('/edit-item/{item}', ['uses'=>"CRUDExampleController@getEditItem", "as"=>"admin-crud-edit-item"]);
	Route::post('/edit-item/{item}', ['uses'=>"CRUDExampleController@postEditItem", "as"=>"admin-crud-edit-item-post"]);

	// Delete Item
	Route::get('/delete-item/{item}', ['uses'=>"CRUDExampleController@getDeleteItem", "as"=>"admin-crud-delete-item"]);
	Route::post('/delete-item/{item}', ['uses'=>"CRUDExampleController@postDeleteItem", "as"=>"admin-crud-delete-item-post"]);

});