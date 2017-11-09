<?php

	//Listing Degree
	Route::get('/candidate-list', ['uses'=>"CandidateController@getListing", 'as'=>"admin-candidate"]);
	Route::post('/candidate-list', ['uses'=>"CandidateController@postListing", 'as'=>"admin-candidate-post"]);	