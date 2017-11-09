<?php

Route::group(['prefix'=>'recruiters', 'namespace'=>'RecruiterControllers'], function() {

	Route::get('/', ['uses'=>"GuestController@getIndex", 'as'=>"recruiter-home"]);

	$routeFiles = File::allFiles(__DIR__."/Recruiter");

	foreach($routeFiles as $routeFile) {
		require($routeFile);
	}

});