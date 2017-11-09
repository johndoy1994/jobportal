<?php 

	//Listing Employer
	Route::get('/employer-list', ['uses'=>"EmployerController@getListing", 'as'=>"admin-employer"]);
	Route::post('/employer-list', ['uses'=>"EmployerController@postListing", 'as'=>"admin-employer-post"]);
	
	//New Employer
	Route::get('/employer-list/new-employer', ['uses'=>"EmployerController@getNewEmployer", 'as'=>"admin-new-employer"]);
	Route::post('/employer-list/new-employer',['uses'=>"EmployerController@postNewEmployer", 'as'=>"admin-new-employer-post"]);

	//Edit Employer
	Route::get('/employer-list/edit-employer/{Employer}',['uses'=>"EmployerController@getEditEmployer", 'as'=>"admin-edit-employer"]);
	Route::post('/employer-list/edit-employer/{Employer}',['uses'=>"EmployerController@postEditEmployer", 'as'=>"admin-edit-employer-post"]);
	Route::get('/employer-list/delete-image',['uses'=>"EmployerController@getDeleteImageEmployer", 'as'=>"admin-edit-employer-delete-image"]);

	//Active Inactive Job
	Route::get('/employer-list/change-action',['uses'=>"EmployerController@getActiveInactiveJob", 'as'=>"admin-active-inactive-employer-post"]);
	Route::post('/employer-list/change-profile-picture',['uses'=>"EmployerController@postSaveProfilePicture", 'as'=>"admin-edit-employer-change-dp"]);

