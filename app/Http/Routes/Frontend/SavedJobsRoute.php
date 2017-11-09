<?php

Route::group(['prefix'=>'saved-jobs', 'namespace'=>'FrontendControllers'], function() {
	Route::get('/', ['uses'=>"SavedJobController@getIndex" , 'as'=>'saved-jobs']);
});