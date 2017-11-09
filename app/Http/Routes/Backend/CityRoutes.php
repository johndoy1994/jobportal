<?php

	//Listing city
	Route::get('/city-list', ['uses'=>"CityController@getListing", 'as'=>"admin-city"]);
	Route::post('/city-list', ['uses'=>"CityController@postListing", 'as'=>"admin-city-post"]);

	//New city
	Route::get('/city-list/new-city', ['uses'=>"CityController@getNewCity", 'as'=>"admin-new-city"]);
	Route::post('/city-list/new-city',['uses'=>"CityController@postNewCity", 'as'=>"admin-new-city-post"]);

	//Edit city
	Route::get('/city-list/edit-city/{City}',['uses'=>"CityController@getEditCity", 'as'=>"admin-edit-city"]);
	Route::post('/city-list/edit-city/{City}',['uses'=>"CityController@postEditCity", 'as'=>"admin-edit-city-post"]);

	//  Delete city
	Route::get('/city-list/delete-city/{City}', ['uses'=>"CityController@postDeleteCity", 'as'=>"admin-delete-city"]);

	