<?php

Route::group(['prefix'=>'messages'] , function() {

	Route::get('/', ['uses'=>"MessagesController@getIndex", 'as'=>'recruiter-message']);
	Route::get('/{conversation_id}', ['uses'=>"MessagesController@getConversation", 'as'=>'recruiter-conversation']);
	Route::get('/download-attachment/{MessageAttachment}/{filename}', ['uses'=>"MessagesController@getDownloadAttachment", 'as'=>'recruiter-downloadAttachment']);
	Route::post('/delete-message', ['uses'=>"MessagesController@postDeleteMessages", 'as'=>'recruiter-delete-message']);

});