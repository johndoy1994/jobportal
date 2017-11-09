<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Models\Job;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SavedJobController extends Controller
{

    public function __construct() {
        parent::__construct();
        
        $getNewMessagesCount = 0;
        if(MyAuth::check()) {
            $user=MyAuth::user();
            $getNewMessagesCount = MessagesRepo::getNewMessagesCount($user);
        }
        View::share("messageCount", $getNewMessagesCount);

       
    }

    public function getIndex(Request $request) {

    	$jobs = [];

    	if(MyAuth::check()) {
    		$_jobs = MyAuth::user()->saved_jobs;
    		foreach($_jobs as $saved_job) {
    			if($saved_job->job) {
     				$jobs[] = $saved_job->job;
     			}
    		}
    	} else {
    		$_jobs = [];
            if(session()->has('saved_jobs')) {
                $_jobs = $request->session()->get('saved_jobs');
            }
            if(is_array($_jobs)) {
        		foreach($_jobs as $job_id => $job) {
        			$job = Job::find($job_id);
        			if($job) {
        				$jobs[] = $job;
        			}
        		}
            }
    	}

    	return view('frontend.saved-jobs', [
    		'jobs' => $jobs
		]);
    }

}
