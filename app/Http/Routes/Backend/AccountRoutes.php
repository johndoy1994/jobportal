<?php

	//Change admin password
   Route::get('/change-password', ['uses'=>"GuestController@getChangePassword", 'as'=>"admin-change-password"]);
   Route::post('/change-password', ['uses'=>"GuestController@postChangePassword", 'as'=>"admin-change-password-post"]);

   // Forgot Password
	Route::get('/forgot-password', ['uses'=>"GuestController@getForgotPassword", "as"=>"admin-account-forgotpassword"]);
	Route::post('/forgot-password', ['uses'=>"GuestController@postForgotPassword", "as"=>"admin-account-forgotpassword-post"]);
