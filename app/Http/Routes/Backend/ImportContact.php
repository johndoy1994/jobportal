<?php

Route::group(['prefix'=>'import-contacts'], function() {
	Route::get('/', ['as'=>'admin-import-contact', 'uses'=>"ImportContactController@getIndex"]);
	Route::get('/listing', ['as'=>'get-admin-import-contact', 'uses'=>"ImportContactController@getlisting"]);
	Route::post('/listing', ['as'=>'post-admin-import-contact', 'uses'=>"ImportContactController@postlisting"]);
	Route::get('/gmail-contacts', ['as'=>'admin-gmail-contacts', 'uses'=>"ImportContactController@getGmailContacts"]);
	Route::post('/gmail-contacts', ['as'=>'admin-gmail-contacts-post', 'uses'=>"ImportContactController@postGmailContacts"]);
});