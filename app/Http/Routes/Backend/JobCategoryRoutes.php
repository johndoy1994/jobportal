<?php

Route::group(['prefix'=>'job-category'], function() {
	
	// Listing
	Route::get('/', ['uses'=>"JobCategoryController@getListing", 'as'=>"admin-job-category"]);
	Route::post('/', ['uses'=>"JobCategoryController@postListing", 'as'=>"admin-job-category-post"]);
	
	// Redirect to new form page
	Route::get('/add-new', ['uses'=>"JobCategoryController@getAddNewItem", 'as'=>"admin-job-category-add-new"]);

	// Submit Form page data 
	Route::post('/add-new', ['uses'=>"JobCategoryController@postAddNewItem", 'as'=>"admin-job-category-add-new-post"]);

	// Redirect to edit Form page
	Route::get('/edit/{item}', ['uses'=>"JobCategoryController@getEditJobCategory",'as'=>"admin-edit-job-category"]);

	// Submit Edit Form Page
	Route::post('/edit/{item}', ['uses'=>"JobCategoryController@postEditJobCategory",'as'=>"admin-edit-job-category-post"]);

	// Deleting
	Route::get('/delete/{item}', ['uses'=>"JobCategoryController@getDeleteJobCategory", 'as'=>"admin-delete-job-category"]);
	
});