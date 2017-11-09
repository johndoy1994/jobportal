<?php
Route::group(['prefix'=>'cms'], function() {
	//Listing Degree
	Route::get('/listing', ['uses'=>"CmsPagesController@getCMSIndex", 'as'=>"admin-cms-page"]);
	Route::post('/listing', ['uses'=>"CmsPagesController@postIndexBulkAction", 'as'=>"admin-cms-index-post"]);
	Route::get('/new', ['uses'=>"CmsPagesController@getAddCmsPage", 'as'=>"admin-new-cmspage"]);
	Route::get('/edit', ['uses'=>"CmsPagesController@getEditCmsPage", 'as'=>"admin-edit-cmspage"]);
	Route::post('/edit', ['uses'=>"CmsPagesController@postSaveCmsPage", 'as'=>"admin-cms-page-post"]);

	Route::get('/change-cms-page-status/', ['uses'=>"CmsPagesController@getActiveInactiveCmsPage", 'as'=>"admin-active-inactive-cms-page"]);
});