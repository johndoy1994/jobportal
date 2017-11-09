<?php

Route::group(['prefix'=>'avatars'], function() {
	Route::get('{id}', function(Request $request, $id) {
		$filename = 'avatars/'.$id.'.png';
		if(Storage::exists($filename)) {
			return response(Storage::get($filename))->header('Content-Type', 'image/png');
		} else {
			return response(Storage::get('avatars/no-image.png'))->header('Content-Type', 'image/png');
		}
	})->name('account-avatar');

	Route::get('100x100/{id}', function(Request $request, $id) {
		$filename = 'avatars/100x100/'.$id.'.png';
		if(Storage::exists($filename)) {
			return response(Storage::get($filename))->header('Content-Type', 'image/png');
		} else {
			return response(Storage::get('avatars/100x100/no-image.png'))->header('Content-Type', 'image/png');
		}
	})->name('account-avatar-100x100');

	Route::get('200x200/{id}', function(Request $request, $id) {
		$filename = 'avatars/200x200/'.$id.'.png';
		if(Storage::exists($filename)) {
			return response(Storage::get($filename))->header('Content-Type', 'image/png');
		} else {
			return response(Storage::get('avatars/200x200/no-image.png'))->header('Content-Type', 'image/png');
		}
	})->name('account-avatar-200x200');

	Route::get('/employer/{id}', function(Request $request, $id) {
		$filename = 'avatars/employers/'.$id.'.png';
		if(Storage::exists($filename)) {
			return response(Storage::get($filename))->header('Content-Type', 'image/png');
		} else {
			return response(Storage::get('avatars/no-company.png'))->header('Content-Type', 'image/png');
		}
	})->name('account-employer-avatar');

});

Route::group(['prefix'=>'download'], function() {
	Route::get('', function(Request $request) {
			return response(Storage::get('avatars/resume.png'))->header('Content-Type', 'image/png');
	})->name('resume-image');
});