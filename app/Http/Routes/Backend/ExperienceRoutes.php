<?php

Route::group(['prefix'=>'experience'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"ExperienceController@getListing", 'as'=>"admin-experience"]);
	Route::post('/', ['uses'=>"ExperienceController@postListing", 'as'=>"admin-experience-post"]);

	Route::get('/experience-list/move-order/{experience}/{action}', ['uses'=>"ExperienceController@postMoveOrder", 'as'=>'admin-experience-moveorder']);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"ExperienceController@getAddNewItem", 'as'=>"admin-experience-add-new"]);

	// // Submit Form page data 
	Route::post('/add-new', ['uses'=>"ExperienceController@postAddNewItem", 'as'=>"admin-experience-add-new-post"]);

	// // Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"ExperienceController@getEditExperience",'as'=>"admin-edit-experience"]);

	// // Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"ExperienceController@postEditExperience",'as'=>"admin-edit-experience-post"]);

	// // Deleting
	Route::get('/delete/{item}', ['uses'=>"ExperienceController@getDeleteExperience", 'as'=>"admin-delete-experience"]);
	
});