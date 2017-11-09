<?php

Route::group(['prefix'=>'api', 'namespace'=>'FrontendControllers\APIControllers'], function() {

	Route::get('/through/{provider}', ['uses'=>"PublicController@getThrough" , 'as'=>"api-through"]);
	Route::get('/through/{provider}/callback', ['uses'=>"PublicController@getThroughCallback", "as"=>"api-through-callback"]);

	Route::group(['prefix'=>'public'] , function() {

		// GET Requests
		Route::get('/countries', ['uses'=>'PublicController@getCountries', 'as'=>'api-public-countries']);
		Route::get('/states', ['uses'=>'PublicController@getStates', 'as'=>'api-public-states']);
		Route::get('/cities', ['uses'=>'PublicController@getCities', 'as'=>'api-public-cities']);
		Route::get('/certificates', ['uses'=>'PublicController@getCertificates', 'as'=>'api-public-certificates']);
		Route::get('/salaryranges', ['uses'=>'PublicController@getSalaryRanges', 'as'=>'api-public-salaryranges']);
		Route::get('/jobtitles', ['uses'=>'PublicController@getJobTitles', 'as'=>'api-public-jobtitles']);
		Route::get('/skills', ['uses'=>'PublicController@getSkills', 'as'=>'api-public-skills']);
		Route::get('/job-keywords', ['uses'=>"PublicController@getJobKeywords", "as"=>"api-public-jobkeywords"]);
		Route::get('/locations', ['uses'=>"PublicController@getLocations", "as"=>"api-public-locations"]);
		Route::get('/jobs', ['uses'=>"PublicController@getJobs", "as"=>"api-public-jobs"]);
		Route::get('/cvs', ['uses'=>"PublicController@getCVs", "as"=>"api-public-cvs"]);
		Route::get('/save-job/{job}', ['uses'=>"PublicController@getSaveJob", 'as'=>'api-public-savejob']);
		Route::get('/create-alert', ['uses'=>"PublicController@getCreateAlert", "as"=>'api-public-createalert']);
		Route::get('/recordperpage',['uses'=>"PublicController@getRecordPerPage","as"=>'api-public-recordPerPage']);
		Route::get('/job-categories', ['uses'=>'PublicController@getJobCategories', 'as'=>'api-public-jobcategories']);
		Route::get('/reset-password', ['uses'=>"PublicController@getResetPassword", "as"=>'api-public-resetpassword']);
		Route::get('/reset-password-link', ['uses'=>"PublicController@getResetPasswordLink", "as"=>"api-public-resetpasswordlink"]);
		Route::get('/expireJobs-inactive', ['uses'=>"PublicController@expireJobsAutosetInactive"]);
		// POST Requests
		Route::post('/save-jobalert-content-type', ['uses'=>"PublicController@postSaveJobAlertContentType", "as"=>"api-public-savejobalertcontenttype"]);
		Route::post('/modify-profile', ['uses'=>"PublicController@postModifyProfile", 'as'=>"api-public-modifyprofile"]);
		Route::post('/job-days', ['uses'=>"PublicController@getJobDays", "as"=>"api-public-jobdays"]);
		Route::post('/reset-password-link', ['uses'=>"PublicController@postResetPasswordLink", "as"=>"api-public-resetpasswordlink-post"]);
		
		Route::get('/view/page', ['uses'=>"PublicController@getViewCmsPage", "as"=>"api-public-view-footer-links"]);
		Route::get('/jobAlert', ['uses'=>'PublicController@jobAlert', 'as'=>'api-job-alert']);
		Route::get('/getcitylat', ['uses'=>'PublicController@getcityLatlong', 'as'=>'getcitylat']);

		Route::get('/get-notification', ['uses'=>'PublicController@getNotification', 'as'=>'api-get-notificationConversation']);

	});

	Route::group(['prefix'=>'messages'], function() {

		Route::post('/new-message', ['uses'=>'SecureController@postNewMessage', 'as'=>'api-messages-newmessage']);
		Route::post('/message-status', ['uses'=>'SecureController@postMessageStatusUpdate', 'as'=>'api-messages-statusUpdate']);
		Route::post('/autochat', ['uses'=>"SecureController@postAutoChat", "as"=>'api-secure-autochat']);
		Route::post('/send-multiuser-message', ['uses'=>'SecureController@postMultipleNewMessage', 'as'=>'api-messages-multiplenewmessage']);

	});

	Route::group(['prefix'=>'notifications'], function() {
		Route::post('/notification-status', ['uses'=>'SecureController@postNotificationStatusUpdate', 'as'=>'api-notification-statusUpdate']);
	});

	Route::post('/update-emailaddress',['uses'=>'SecureController@postUpdateEmailAddress','as'=>'api-update-email-address']);	
	Route::post('/show-job-application', ['uses'=>"SecureController@postShowJobApplication", "as"=>"api-secure-showjobapplication"]);
	Route::post('/update-job-application-dates', ['uses'=>"SecureController@postUpdateJobApplicationDates", "as"=>"api-secure-updatedatesjobapplication"]);
	Route::post('/sendemail-applicant', ['uses'=>"SecureController@postSendEmailApplication", "as"=>"api-email-content"]);
	Route::get('/candidate-job-matches', ['uses'=>"SecureController@getJobMatchStatus", "as"=>"api-job-matchstatus"]);
	Route::post('/sendemail-multiuser', ['uses'=>"SecureController@postSendEmailMultiuser", "as"=>"api-email-contentmultiuser"]);

});