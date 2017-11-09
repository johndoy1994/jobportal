<?php

	//Listing Degree
	Route::get('/degree-list', ['uses'=>"DegreeController@getListing", 'as'=>"admin-degree"]);
	Route::post('/degree-list', ['uses'=>"DegreeController@postListing", 'as'=>"admin-degree-post"]);

	//New Degree
	Route::get('/degree-list/new-degree', ['uses'=>"DegreeController@getNewDegree", 'as'=>"admin-new-degree"]);
	Route::post('/degree-list/new-degree',['uses'=>"DegreeController@postNewDegree", 'as'=>"admin-new-degree-post"]);

	//Edit Degree
	Route::get('/degree-list/edit-degree/{Degree}',['uses'=>"DegreeController@getEditDegree", 'as'=>"admin-edit-degree"]);
	Route::post('/degree-list/edit-degree/{Degree}',['uses'=>"DegreeController@postEditDegree", 'as'=>"admin-edit-degree-post"]);

	//  Delete Degree
	Route::get('/degree-list/delete-degree/{Degree}', ['uses'=>"DegreeController@postDeleteDegree", 'as'=>"admin-delete-degree"]);
	