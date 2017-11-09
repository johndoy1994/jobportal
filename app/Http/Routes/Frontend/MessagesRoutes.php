<?php

Route::group(['prefix'=>'messages', 'namespace' => "FrontendControllers"] , function() {

	Route::get('/', ['uses'=>"MessagesController@getIndex", 'as'=>'frontend-message']);
	Route::get('/{conversation_id}', ['uses'=>"MessagesController@getConversation", 'as'=>'frontend-conversation']);
	Route::get('/download-attachment/{MessageAttachment}/{filename}', ['uses'=>"MessagesController@getDownloadAttachment", 'as'=>'frontend-downloadAttachment']);
	Route::post('/delete-message', ['uses'=>"MessagesController@postDeleteMessages", 'as'=>'frontend-delete-message']);
	

});