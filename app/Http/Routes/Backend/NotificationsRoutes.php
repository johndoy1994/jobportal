<?php

	Route::get('/Notifications', ['uses'=>"NotificationsController@getNotificationsIndex", 'as'=>'backend-notifications']);
	Route::get('/Notifications/{conversation_id}', ['uses'=>"NotificationsController@getConversation", 'as'=>'backend-conversation']);
	Route::get('/Notifications/download-attachment/{MessageAttachment}/{filename}', ['uses'=>"NotificationsController@getDownloadAttachment", 'as'=>'backend-downloadAttachment']);

