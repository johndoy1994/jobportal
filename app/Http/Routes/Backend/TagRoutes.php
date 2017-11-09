<?php

	//Listing Tag
	Route::get('/tags', ['uses'=>"TagController@getListing", 'as'=>"admin-tag"]);
	Route::post('/tags', ['uses'=>"TagController@postListing", 'as'=>"admin-tag-post"]);

	//New Tag
	Route::get('/tags/new-tag', ['uses'=>"TagController@getNewTag", 'as'=>"admin-new-tag"]);
	Route::post('/tags/new-tag',['uses'=>"TagController@postNewTag", 'as'=>"admin-new-tag-post"]);

	//Edit tag
	Route::get('/tags/edit-tag/{Tag}',['uses'=>"TagController@getEditTag", 'as'=>"admin-edit-tag"]);
	Route::post('/tags/edit-tag/{Tag}',['uses'=>"TagController@postEditTag", 'as'=>"admin-edit-tag-post"]);

	//  Delete Tag
	Route::get('/Tags/delete-tag/{Tag}', ['uses'=>"TagController@postDeleteTag", 'as'=>"admin-delete-tag"]);
	