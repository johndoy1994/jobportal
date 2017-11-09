<?php
/**Job Type**/
	
	//Listing JObType
	Route::get('/jobtypes', ['uses'=>"JobTypeController@getListing", 'as'=>"admin-jobtype"]);	
	Route::post('/jobtypes', ['uses'=>"JobTypeController@postListing", "as"=>"admin-jobtype-post"]);

	Route::get('/jobtypes/move-order/{JobType}/{action}', ['uses'=>"JobTypeController@postMoveOrder", 'as'=>'admin-jobtype-moveorder']);

	// New JObType
	Route::get('/jobtypes/new-jobtype', ['uses'=>"JobTypeController@getNewJobType", 'as'=>"admin-new-jobtype"]);
	Route::post('/jobtypes/new-jobtype', ['uses'=>"JobTypeController@postNewJobType", 'as'=>"admin-new-jobtype-post"]);

	// Edit JObType
	Route::get('/jobtypes/edit-jobtype/{JobType}', ['uses'=>"JobTypeController@getEditJobType", "as"=>"admin-edit-jobtype"]);
	Route::post('/jobtypes/edit-jobtype/{JobType}', ['uses'=>"JobTypeController@postEditJobType", "as"=>"admin-edit-jobtype-post"]);

	
	//  Delete Item
	Route::get('/jobtypes/delete-jobtype/{JobType}', ['uses'=>"JobTypeController@postDeleteJobType", "as"=>"admin-delete-jobtype"]);
	
