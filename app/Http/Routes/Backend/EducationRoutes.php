<?php
	
	//Listing Education
	Route::get('/education-list', ['uses'=>"EducationController@getListing", 'as'=>"admin-education"]);	
	Route::post('/education-list', ['uses'=>"EducationController@postListing", "as"=>"admin-education-post"]);

	Route::get('/education-list/move-order/{education}/{action}', ['uses'=>"EducationController@postMoveOrder", 'as'=>'admin-education-moveorder']);

	// New Education
	Route::get('/education-list/new-education', ['uses'=>"EducationController@getNewEducation", 'as'=>"admin-new-education"]);
	Route::post('/education-list/new-education', ['uses'=>"EducationController@postNewEducation", 'as'=>"admin-new-education-post"]);

	// Edit Education
	Route::get('/education-list/edit-education/{Education}', ['uses'=>"EducationController@getEditEducation", "as"=>"admin-edit-education"]);
	Route::post('/education-list/edit-education/{Education}', ['uses'=>"EducationController@postEditEducation", "as"=>"admin-edit-education-post"]);
	
	//  Delete Education
	Route::get('/education-list/delete-education/{Education}', ['uses'=>"EducationController@postDeleteEducation", "as"=>"admin-delete-education"]);
	
	