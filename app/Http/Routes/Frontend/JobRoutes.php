<?php

Route::group(['prefix'=>'job', 'namespace' => "FrontendControllers"] , function() {

	Route::get('{job}', ['uses'=>"JobController@getJob", 'as'=>'frontend-job']);
	Route::get('{job}/apply', ['uses'=>'JobController@getApplyJob', 'as'=>'frontend-job-apply']);

	Route::post('{job}/apply-as-guest', ['uses'=>'JobController@postApplyAsGuest', 'as'=>'frontend-job-applyasguest']);

});