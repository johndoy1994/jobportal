<?php

Route::group(['prefix'=>'import-contacts', 'namespace'=>'AccountControllers'], function() {
	Route::get('/', ['as'=>'recruiter-import-contact', 'uses'=>"ImportContactController@getIndex"]);
	Route::get('/listing', ['as'=>'get-recruiter-import-contact', 'uses'=>"ImportContactController@getlisting"]);
	Route::post('/listing', ['as'=>'post-recruiter-import-contact', 'uses'=>"ImportContactController@postlisting"]);
	Route::get('/gmail-contacts', ['as'=>'recruiter-gmail-contacts', 'uses'=>"ImportContactController@getGmailContacts"]);
	Route::post('/gmail-contacts', ['as'=>'recruiter-gmail-contacts-post', 'uses'=>"ImportContactController@postGmailContacts"]);
});