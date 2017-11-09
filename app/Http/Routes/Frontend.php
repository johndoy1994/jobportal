<?php

Route::get('/', ['uses'=>"GuestController@getHome", 'as'=>"front-home"]);

$routeFiles = File::allFiles(__DIR__."/Frontend");

foreach($routeFiles as $routeFile) {
	require($routeFile);
}