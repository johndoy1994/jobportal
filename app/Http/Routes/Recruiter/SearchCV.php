<?php

Route::group(['prefix'=>'search-cv'], function() {
	Route::get('/', ['uses'=>'SearchCVController@getIndex', 'as'=>'recruiter-search-cv']);
	Route::get('/cv', ['uses'=>'SearchCVController@getCvDetail', 'as'=>'recruiter-cv-detail']);
	Route::get('/cv/download-cv/{id}', ['uses'=>"SearchCVController@getResumeDownload", 'as'=>'recruiter-user-resumes-download']);
});