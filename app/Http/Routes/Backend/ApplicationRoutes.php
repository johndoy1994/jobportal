<?php

	//Listing application
	Route::get('/application-list', ['uses'=>"ApplicationController@getListing", 'as'=>"admin-application"]);
	Route::post('/application-list', ['uses'=>"ApplicationController@postListing", 'as'=>"admin-application-post"]);	

	//disply full application details
	Route::get('/user-details/{JobApplication}',['uses'=>"ApplicationController@getShowJobApplication", 'as'=>"admin-showjobapplication"]);
	Route::post('/user-details/application-status-change',['uses'=>"ApplicationController@postJobApplicationStatusChange","as"=>'api-public-applicationStatus']);
	Route::get('/user-details/download-cv/{id}', ['uses'=>"ApplicationController@getResumeDownload", 'as'=>'admin-user-resumes-download']);
