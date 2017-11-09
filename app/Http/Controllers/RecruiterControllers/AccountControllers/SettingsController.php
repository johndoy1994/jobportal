<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\MyAuth;
use Illuminate\Http\Request;

class SettingsController extends RecruiterController
{
    public function getIndex() {
    	return view('recruiter.account.setting.index',[
			'user' => MyAuth::user('recruiter')
		]);
    }
}
