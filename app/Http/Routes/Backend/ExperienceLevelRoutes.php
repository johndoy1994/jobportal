<?php

Route::group(['prefix'=>'experience-level'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"ExpLevelController@getListing", 'as'=>"admin-exp-level"]);
	Route::post('/', ['uses'=>"ExpLevelController@postListing", 'as'=>"admin-exp-level-post"]);

	Route::get('/experience-level-list/move-order/{item}/{action}', ['uses'=>"ExpLevelController@postMoveOrder", 'as'=>'admin-exp-level-moveorder']);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"ExpLevelController@getAddNewItem", 'as'=>"admin-exp-level-add-new"]);

	// Submit Form page data 
	Route::post('/add-new', ['uses'=>"ExpLevelController@postAddNewItem", 'as'=>"admin-exp-level-add-new-post"]);

	// Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"ExpLevelController@getEditExperienceLevel",'as'=>"admin-edit-exp-level"]);

	// Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"ExpLevelController@postEditExperienceLevel",'as'=>"admin-edit-exp-level-post"]);

	// Deleting
	Route::get('/delete/{item}', ['uses'=>"ExpLevelController@getDeleteExperienceLevel", 'as'=>"admin-delete-exp-level"]);
	
});