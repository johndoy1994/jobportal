<?php

	//Listing user
	Route::get('/user-list', ['uses'=>"UserController@getListing", 'as'=>"admin-user-list"]);
	Route::post('/user-list', ['uses'=>"UserController@postListing", 'as'=>"admin-user-post"]);	
	Route::get('/user-list/change-status',['uses'=>"UserController@getActiveInactiveUser", 'as'=>"admin-active-inactive-user-post"]);

	//edit user
	Route::get('/user-list/edit-employer/{user}',['uses'=>"UserController@getEditUser", 'as'=>"admin-edit-user"]);
	Route::post('/user-list/edit-employer/{user}',['uses'=>"UserController@postEditEmployer", 'as'=>"admin-edit-EmployerUser-post"]);
	Route::post('/user-list/edit-admin/{user}',['uses'=>"UserController@postEditAdmin", 'as'=>"admin-edit-admin-post"]);
	Route::post('/user-list/edit-jobseeker/{user}',['uses'=>"UserController@postEditJobseeker", 'as'=>"admin-edit-jobseeker-post"]);


	//  Delete user
	Route::get('/user-list/delete-user/{user}', ['uses'=>"UserController@postDeleteUser", 'as'=>"admin-delete-user"]);