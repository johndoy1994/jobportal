<?php

Route::group(['prefix'=>'job-search', 'namespace'=>'FrontendControllers'], function() {

	Route::get('/',['uses'=>'JobSearchController@getIndex','as'=>'job-search']);
	Route::get('/job-detail',['uses'=>'JobSearchController@getJobDetail','as'=>'job-detail']);

});