<?php

	//Listing Recruitertype
	Route::get('/recruitertypes', ['uses'=>"RecruiterTypeController@getListing", 'as'=>"admin-recruitertype"]);
	Route::post('/recruitertypes', ['uses'=>"RecruiterTypeController@postListing", 'as'=>"admin-recruitertype-post"]);

	//New Recruitertype
	Route::get('/recruitertypes/new-recruitertype', ['uses'=>"RecruiterTypeController@getNewRecruitertype", 'as'=>"admin-new-recruitertype"]);
	Route::post('/recruitertypes/new-recruitertype',['uses'=>"RecruiterTypeController@postNewRecruitertype", 'as'=>"admin-new-recruitertype-post"]);

	//Edit Recruitertype
	Route::get('/recruitertypes/edit-recruitertype/{RecruiterType}',['uses'=>"RecruiterTypeController@getEditRecruitertype", 'as'=>"admin-edit-recruitertype"]);
	Route::post('/recruitertypes/edit-recruitertype/{RecruiterType}',['uses'=>"RecruiterTypeController@postEditRecruitertype", 'as'=>"admin-edit-recruitertype-post"]);

	//  Delete Recruitertype
	Route::get('/recruitertypes/delete-recruitertype/{RecruiterType}', ['uses'=>"RecruiterTypeController@postDeleteRecruitertype", 'as'=>"admin-delete-recruitertype"]);
	