<?php

	//Listing Tag
	Route::get('/country-list', ['uses'=>"CountryController@getListing", 'as'=>"admin-country"]);
	Route::post('/country-list', ['uses'=>"CountryController@postListing", 'as'=>"admin-country-post"]);

	//New Tag
	Route::get('/country-list/new-country', ['uses'=>"CountryController@getNewCountry", 'as'=>"admin-new-country"]);
	Route::post('/country-list/new-country',['uses'=>"CountryController@postNewCountry", 'as'=>"admin-new-country-post"]);

	//Edit tag
	Route::get('/country-list/edit-country/{Country}',['uses'=>"CountryController@getEditCountry", 'as'=>"admin-edit-country"]);
	Route::post('/country-list/edit-country/{Country}',['uses'=>"CountryController@postEditCountry", 'as'=>"admin-edit-country-post"]);

	//  Delete Tag
	Route::get('/country-list/delete-country/{Country}', ['uses'=>"CountryController@postDeleteCountry", 'as'=>"admin-delete-country"]);
	