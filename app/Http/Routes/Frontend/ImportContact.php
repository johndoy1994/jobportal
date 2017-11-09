<?php

Route::group(['namespace'=>'FrontendControllers'], function() {
	Route::group(['prefix'=>'import-contacts', 'namespace'=>'AccountControllers'], function() {
		Route::get('/', ['as'=>'jobseeker-import-contact', 'uses'=>"ImportContactController@getIndex"]);
		Route::get('/listing', ['as'=>'get-jobseeker-import-contact', 'uses'=>"ImportContactController@getlisting"]);
		Route::post('/listing', ['as'=>'post-jobseeker-import-contact', 'uses'=>"ImportContactController@postlisting"]);
		Route::get('/gmail-contacts', ['as'=>'jobseeker-gmail-contacts', 'uses'=>"ImportContactController@getGmailContacts"]);
		Route::post('/gmail-contacts', ['as'=>'jobseeker-gmail-contacts-post', 'uses'=>"ImportContactController@postGmailContacts"]);
	});
});