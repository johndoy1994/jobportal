<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;

class ApplicationController extends RecruiterController
{
    public function getIndex() {

    	$postedJobs=[];
    	$candidate=[];
    	if(MyAuth::check('recruiter')) {
            $user = MyAuth::user('recruiter');
           	$postedJobs= PublicRepo::getPostedJob($user);
        }
        foreach ($postedJobs as $key => $value) {
        	$candidate[$key]= PublicRepo::getAllJobApplication($value['id']);
        }

    	return view('recruiter.account.application.index',[
			'user' => MyAuth::user('recruiter'),
			'jobs'=>$postedJobs,
			'candidate'=>$candidate,
			'type'=>"application",
            'view'=>'applicant'
    	]);
    }
}
