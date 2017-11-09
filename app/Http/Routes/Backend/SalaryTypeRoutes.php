<?php

Route::group(['prefix'=>'salary-type'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"SalaryTypeController@getListing", 'as'=>"admin-salary-type"]);
	Route::post('/', ['uses'=>"SalaryTypeController@postListing", 'as'=>"admin-salary-type-post"]);

	Route::get('/move-order/{salarytype}/{action}', ['uses'=>"SalaryTypeController@postMoveOrder", 'as'=>'admin-salarytype-moveorder']);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"SalaryTypeController@getAddNewItem", 'as'=>"admin-salary-type-add-new"]);

	// Submit Form page data 
	Route::post('/add-new', ['uses'=>"SalaryTypeController@postAddNewItem", 'as'=>"admin-salary-type-add-new-post"]);

	// Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"SalaryTypeController@getEditSalaryType",'as'=>"admin-edit-salary-type"]);

	// Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"SalaryTypeController@postEditSalaryType",'as'=>"admin-edit-salary-type-post"]);

	// Deleting
	Route::get('/delete/{item}', ['uses'=>"SalaryTypeController@getDeleteSalaryType", 'as'=>"admin-delete-salary-type"]);
	
});