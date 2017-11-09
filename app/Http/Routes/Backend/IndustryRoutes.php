<?php 	
	//list Industry
	Route::get('/industry-list', ['uses'=>"IndustryController@getListing", 'as'=>"admin-industry"]);
	Route::post('/industry-list', ['uses'=>"IndustryController@postListing", 'as'=>"admin-industry-post"]);

	//New Industry
	Route::get('/industry-list/new-industry', ['uses'=>"IndustryController@getNewIndustry", 'as'=>"admin-new-industry"]);
	Route::post('/industry-list/new-industry',['uses'=>"IndustryController@postNewIndustry", 'as'=>"admin-new-industry-post"]);

	//Edit Industry
	Route::get('/industry-list/edit-industry/{Industry}',['uses'=>"IndustryController@getEditIndustry", 'as'=>"admin-edit-industry"]);
	Route::post('/industry-list/edit-industry/{Industry}',['uses'=>"IndustryController@postEditIndustry", 'as'=>"admin-edit-industry-post"]);

	//  Delete Industry
	Route::get('/industry-list/delete-industry/{Industry}', ['uses'=>"IndustryController@postDeleteIndustry", 'as'=>"admin-delete-industry"]);
	