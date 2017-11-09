<?php

require("Routes/Frontend.php");
require("Routes/Backend.php");
require("Routes/Recruiter.php");

Route::get('/secret-uri', function(\Illuminate\Http\Request $request) {
	
	// echo "Requested : ".$request->id."<br/>";

	// $job = Job::find($request->id);

	// $jobAlerts = PublicRepo::searchJobAlerts($job->jobTitle->job_category_id, $job->job_title_id, "", 0, $job->addresses()->first()->city_id, $job->salary_type_id, $job->salary, $job->job_type_id, 0, $job->title, $job->getJobCoordinates());

	// echo '<pre>';

	// print_r($jobAlerts);

	// echo '</pre>';

});

// $routeGroups = \App\RouteGroup::all();
// foreach($routeGroups as $routeGroup) {
// 	foreach($routeGroup->routes as $route) {
// 		Route::{$route->method}($routeGroup->prefix . "/" . $route->path, ['uses'=>$route->uses, 'as'=>$route->as, 'middleware'=>json_decode($route->middlewares, true)]);
// 	}
// }