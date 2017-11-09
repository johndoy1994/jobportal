<?php

Route::group(['prefix'=>'subscribed-jobs', 'namespace'=>'FrontendControllers'], function() {

	Route::get('/', ['uses'=>"SubscribedJobsController@getIndex", "as"=>"frontend-subscribedjobs"]);

});