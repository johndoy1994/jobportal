<?php

	Route::get('/Messages', ['uses'=>"MessagesController@getMessageIndex", 'as'=>'backend-message']);
	Route::get('/Messages/{conversation_id}', ['uses'=>"MessagesController@getConversation", 'as'=>'backend-conversation']);
	Route::get('/Messages/download-attachment/{MessageAttachment}/{filename}', ['uses'=>"MessagesController@getDownloadAttachment", 'as'=>'backend-downloadAttachment']);

