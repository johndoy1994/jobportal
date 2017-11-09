<?php

	//Listing state
	Route::get('/state-list', ['uses'=>"StateController@getListing", 'as'=>"admin-state"]);
	Route::post('/state-list', ['uses'=>"StateController@postListing", 'as'=>"admin-state-post"]);

	//New state
	Route::get('/state-list/new-state', ['uses'=>"StateController@getNewState", 'as'=>"admin-new-state"]);
	Route::post('/state-list/new-state',['uses'=>"StateController@postNewState", 'as'=>"admin-new-state-post"]);

	//Edit state
	Route::get('/state-list/edit-state/{State}',['uses'=>"StateController@getEditState", 'as'=>"admin-edit-state"]);
	Route::post('/state-list/edit-state/{State}',['uses'=>"StateController@postEditState", 'as'=>"admin-edit-state-post"]);

	//  Delete state
	Route::get('/state-list/delete-state/{State}', ['uses'=>"StateController@postDeleteState", 'as'=>"admin-delete-state"]);
	
	