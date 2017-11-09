<?php

Route::group(['prefix'=>"account", "namespace"=>"FrontendControllers"], function() {

	// Account Home
	Route::get('/', ['uses'=>'AccountController@getIndex', 'as'=>'account-home']);

	// Verification
	Route::get('/verification', ['uses'=>'AccountController@getVerification', 'as'=>'account-verification']);
	Route::post('/verification', ['uses'=>'AccountController@postVerification', 'as'=>'account-verification-post']);

	// Logout
	Route::get('/signout', ['uses'=>"AccountController@getSignOut", 'as'=>"account-signout"]);

	// Register
	Route::get('/register', ['uses'=>"AccountController@getRegister", "as"=>"account-register"]);
	Route::post('/register', ['uses'=>"AccountController@postRegister", "as"=>"account-register-post"]);

	// Sign In
	Route::get('/signin', ['uses'=>"AccountController@getSignIn", 'as'=>"account-signin"]);
	Route::post('/signin', ['uses'=>"AccountController@postSignIn", 'as'=>"account-signin-post"]);

	// Sign In with selected cookie account
	Route::get('/signin-with-account', ['uses'=>"AccountController@getSignInWithCookieAccount", 'as'=>"selected-cookie-account"]);

	// Forgot Password
	Route::get('/forgot-password', ['uses'=>"AccountController@getForgotPassword", "as"=>"account-forgotpassword"]);
	Route::post('/forgot-password', ['uses'=>"AccountController@postForgotPassword", "as"=>"account-forgotpassword-post"]);

	// Profile
	Route::group(['prefix'=>"my-profile", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"MyProfileController@getIndex", 'as'=>'account-myprofile']);
		Route::post('/save', ['uses'=>"MyProfileController@postSaveProfile", 'as'=>'account-save-myprofile']);
		Route::post('/save-profile-image', ['uses'=>"MyProfileController@postSaveProfilePicture", 'as'=>'account-save-profilepicture']);
	});

	// Socialite Routes
	Route::get('/through/{provider}', ['uses'=>"AccountController@getThrough" , 'as'=>"account-through"]);
	Route::get('/through/{provider}/callback', ['uses'=>"AccountController@getThroughCallback", "as"=>"account-through-callback"]);


	////////////////////////////////////////////////////
	/// Sagar Routes ///////////////////////////////////
	////////////////////////////////////////////////////

	//Chenge Password
	Route::group(['prefix'=>"change-password", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"ChangePasswordController@getIndex", 'as'=>'account-changepassword']);
		Route::post('/save', ['uses'=>"ChangePasswordController@postSave", 'as'=>'account-save-changepassword']);
	});

	//Delete Account
	Route::group(['prefix'=>"delete-account", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"DeleteAccountController@getIndex", 'as'=>'account-delete']);
		Route::post('/delete-account', ['uses'=>"DeleteAccountController@postDeleteAccount", 'as'=>'account-delete-post']);
	});

	//Settings Account
	Route::group(['prefix'=>"settings", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"SettingsController@getIndex", 'as'=>'account-settings']);

		Route::post('/save-profile-visibility', ['uses'=>"SettingsController@postSaveProfileVisibility", "as"=>"account-settings-save-profilevisibility"]);
		Route::post('/save-instant-match', ['uses'=>"SettingsController@postInstantMatch", "as"=>"account-settings-save-instantmatch"]);

		// Mohan
		Route::group(['prefix'=>'job-alerts'], function() {

		});

	});

	//User Resume
	Route::group(['prefix'=>"resumes", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"UserResumeController@getIndex", 'as'=>'account-user-resumes']);
		Route::post('/save', ['uses'=>"UserResumeController@postIndex", 'as'=>'account-user-resumes-post']);
		Route::get('/download/{id}', ['uses'=>"UserResumeController@getResumeDownload", 'as'=>'account-user-resumes-download']);
	});

	//Job Application
	Route::group(['prefix'=>"job-application", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"JobApplicationController@getIndex", 'as'=>'account-job-application']);
		Route::post('/cancel', ['uses'=>"JobApplicationController@postCancelApplication", 'as'=>'account-cancel-job-application']);
	});

	//Job Alerts
	Route::group(['prefix'=>"job-alerts", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"JobAlertsController@getIndex", 'as'=>'account-job-alerts']);

		Route::post('/save', ['uses'=>"SettingsController@postSaveJobAlert", "as"=>"account-settings-save-jobalert"]);
		Route::get('/update/{alert}/{action}', ['uses'=>"SettingsController@getUpdateJobAlertStatus", "as"=>"account-settings-update-jobalert-status"]);
		Route::get('/edit/{alert}', ['uses'=>"SettingsController@getEditJobAlert", "as"=>"account-settings-edit-jobalert"]);		
		Route::get('/create', ['uses'=>"SettingsController@getCreateJobAlert", "as"=>"account-settings-create-jobalert"]);		
	});

	//Notification
	Route::group(['prefix'=>"notification", "namespace"=>"AccountControllers"], function() {
		Route::get('/', ['uses'=>"NotificationController@getIndex", 'as'=>'account-notification']);
	});


});