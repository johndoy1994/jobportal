<?php

	//Listing user
	Route::get('/communication', ['uses'=>"CommunicationController@getListing", 'as'=>"admin-communication"]);
	