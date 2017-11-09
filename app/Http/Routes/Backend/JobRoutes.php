<?php

	//Listing Job
	Route::get('/job-list', ['uses'=>"JobController@getListing", 'as'=>"admin-job"]);
	Route::post('/job-list', ['uses'=>"JobController@postListing", 'as'=>"admin-job-post"]);

	//New Job
	Route::get('/job-list/new-job', ['uses'=>"JobController@getNewJob", 'as'=>"admin-new-job"]);
	Route::post('/job-list/new-job',['uses'=>"JobController@postNewJob", 'as'=>"admin-new-job-post"]);

	//Edit Job
	Route::get('/job-list/edit-job/{Job}',['uses'=>"JobController@getEditJob", 'as'=>"admin-edit-job"]);
	Route::post('/job-list/edit-job/{Job}',['uses'=>"JobController@postEditJob", 'as'=>"admin-edit-job-post"]);

	//Active Inactive Job
	Route::get('/job-list/change-action',['uses'=>"JobController@getActiveInactiveJob", 'as'=>"admin-active-inactive-job-post"]);

	//Repost Job
	Route::get('/job-list/repost-job/{Job}', ['uses'=>"JobController@getRepostJob", 'as'=>"admin-repost-job"]);
	Route::post('/job-list/repost-job/{Job}',['uses'=>"JobController@postRepostJob", 'as'=>"admin-repost-job-post"]);

	//Renew Job
	Route::get('/job-list/renew-job/{Job}', ['uses'=>"JobController@getRenewJob", 'as'=>"admin-renew-job"]);
	Route::post('/job-list/renew-job/{Job}',['uses'=>"JobController@postRenewJob", 'as'=>"admin-renew-job-post"]);