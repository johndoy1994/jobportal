<?php

Route::group(['prefix'=>'job-title'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"JobTitleController@getListing", 'as'=>"admin-job-title"]);
	Route::post('/', ['uses'=>"JobTitleController@postListing", 'as'=>"admin-job-title-post"]);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"JobTitleController@getAddNewItem", 'as'=>"admin-job-title-add-new"]);

	// // Submit Form page data 
	Route::post('/add-new', ['uses'=>"JobTitleController@postAddNewItem", 'as'=>"admin-job-title-add-new-post"]);

	// // Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"JobTitleController@getEditJobTitle",'as'=>"admin-edit-job-title"]);

	// // Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"JobTitleController@postEditJobTitle",'as'=>"admin-edit-job-title-post"]);

	// // Deleting
	Route::get('/delete/{item}', ['uses'=>"JobTitleController@getDeleteJobCategory", 'as'=>"admin-delete-job-title"]);
	
});