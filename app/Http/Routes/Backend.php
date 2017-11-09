<?php

Route::group(['prefix'=>"admin", 'namespace'=>'BackendControllers'], function() {

	Route::get('/', ['uses'=>"GuestController@getIndex", 'as'=>"admin-login"]);
	Route::post('/admin-login', ['uses'=>"GuestController@postIndex", 'as'=>"admin-login-post"]);
	
	Route::get('/dashboard', ['uses'=>"BackendController@getIndex", 'as'=>"admin-home"]);
	Route::get('/logout', ['uses'=>"GuestController@getLogout", 'as'=>"admin-logout"]);

	$routeFiles = File::allFiles(__DIR__."/Backend");

	foreach($routeFiles as $routeFile) {
		require($routeFile);
	}

});