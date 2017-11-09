<?php
Route::group(['prefix'=>'salary-range'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"SalaryRangeController@getListing", 'as'=>"admin-salary-range"]);
	Route::post('/', ['uses'=>"SalaryRangeController@postListing", 'as'=>"admin-salary-range-post"]);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"SalaryRangeController@getAddNewItem", 'as'=>"admin-salary-range-add-new"]);

	// Submit Form page data 
	Route::post('/add-new', ['uses'=>"SalaryRangeController@postAddNewItem", 'as'=>"admin-salary-range-add-new-post"]);

	// Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"SalaryRangeController@getEditSalaryrange",'as'=>"admin-edit-salary-range"]);

	// Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"SalaryRangeController@postEditSalaryrange",'as'=>"admin-edit-salary-range-post"]);

	// Deleting
	Route::get('/delete/{item}', ['uses'=>"SalaryRangeController@getDeleteSalaryType", 'as'=>"admin-delete-salary-range"]);
});