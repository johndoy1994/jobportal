<?php
Route::group(['prefix'=>"account", "namespace"=>"AccountControllers"], function() {
	
	// Account Home
	Route::get('/', ['uses'=>'AccountController@getIndex', 'as'=>'recruiter-account-home']);

	//Chenge Password
	Route::get('/change-password', ['uses'=>"ChangePasswordController@getIndex", 'as'=>'recruiter-account-changepassword']);
	Route::post('/change-password', ['uses'=>"ChangePasswordController@postSave", 'as'=>'recruiter-account-changepassword-post']);

	//Delete Account
	Route::get('/delete-account', ['uses'=>"DeleteAccountController@getIndex", 'as'=>'recruiter-account-delete']);
	Route::post('/delete-account', ['uses'=>"DeleteAccountController@postDeleteAccount", 'as'=>'account-delete-post']);
	//Account Setting
	Route::get('/setting', ['uses'=>"SettingsController@getIndex", 'as'=>'recruiter-account-settings']);

	//Account Notifications
	Route::get('/notifications', ['uses'=>"NotificationsController@getIndex", 'as'=>'recruiter-account-notifications']);

	//Posted Jobsrecruiter-job-delete
	Route::get('/posted-jobs', ['uses'=>"PostedJobsController@getIndex", 'as'=>'recruiter-posted-jobs']);
	Route::get('/posted-jobs/change-action',['uses'=>"PostedJobsController@getActiveInactiveJob", 'as'=>"recruiter-active-inactive-job-post"]);
	Route::get('/delete-postedjob/{job}', ['uses'=>"PostedJobsController@getDeletePostedJob", 'as'=>'recruiter-job-delete']);
	Route::get('/application-details/{job}', ['uses'=>"PostedJobsController@getApplicationDetais", 'as'=>'recruiter-application-details']);
	Route::get('/jobapplication-saved/{JobApplication}', ['uses'=>"PostedJobsController@getApplicationDetailSaved", 'as'=>'recruiter-applicationdetails-saved']);
	Route::get('/jobapplication-delete/{JobApplication}', ['uses'=>"PostedJobsController@getApplicationDetailDelete", 'as'=>'recruiter-applicationdetails-delete']);
	Route::get('/postedjob-details/{job}', ['uses'=>"PostedJobsController@getPostedJobDetais", 'as'=>'recruiter-postedjob-details']);
	//Route::get('/user-chat/{JobApplication}', ['uses'=>"PostedJobsController@getApplicationDetailDelete", 'as'=>'recruiter-applicationdetails-delete']);

	//Candidates
	Route::get('/candidates', ['uses'=>"CandidatesController@getIndex", 'as'=>'recruiter-candidates']);
	Route::get('/candidates/{UserId}', ['uses'=>"CandidatesController@getCandidateDetails", 'as'=>'recruiter-candidatesdetails']);

	//application
	Route::get('/applications', ['uses'=>"ApplicationController@getIndex", 'as'=>'recruiter-application']);

	//Post New Job
	Route::group(['prefix'=>'job'], function() {
		Route::get('/{mode}/{job?}', ['uses'=>"JobController@getIndex", 'as'=>'recruiter-job']);
		Route::post('/{mode}/{job?}', ['uses'=>"JobController@postJob"]);
	});
	
	//Recruiter sign in 
	Route::get('/signin', ['uses'=>"AccountController@getSignIn", 'as'=>"recruiter-account-signin"]);
	Route::post('/signin', ['uses'=>"AccountController@postSignIn", 'as'=>"recruiter-account-signin-post"]);

	// Sign In with selected cookie account
	Route::get('/signin-with-account', ['uses'=>"AccountController@getSignInWithCookieAccount", 'as'=>"recruiter-selected-cookie-account"]);

	// Register
	Route::get('/register', ['uses'=>"AccountController@getRegister", "as"=>"recruiter-account-register"]); 
	Route::post('/register', ['uses'=>"AccountController@postRegister", "as"=>"recruiter-account-register-post"]);
	// Logout
	Route::get('/signout', ['uses'=>"AccountController@getSignOut", 'as'=>"recruiter-account-signout"]);	

	//recriuter profile
	Route::post('/save-profile-image', ['uses'=>"AccountController@postSaveProfilePicture", 'as'=>'recruiter-account-save-profilepicture']);

	// Forgot Password
	Route::get('/forgot-password', ['uses'=>"AccountController@getForgotPassword", "as"=>"recruiter-account-forgotpassword"]);
	Route::post('/forgot-password', ['uses'=>"AccountController@postForgotPassword", "as"=>"recruiter-account-forgotpassword-post"]);

	//change profile
	Route::get('/company-profile', ['uses'=>"AccountController@getCompanyProfile", "as"=>"recruiter-company-profile"]);
	Route::post('/company-profile', ['uses'=>"AccountController@postCompanyProfile", "as"=>"recruiter-company-profile-post"]);
});


