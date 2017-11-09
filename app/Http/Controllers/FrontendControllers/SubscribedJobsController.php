<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;

class SubscribedJobsController extends FrontendController
{
    public function getIndex(Request $request) {

    	$alert = 0;

    	if($request->has('alert') && is_numeric($request->alert)) {
    		$alert = $request->alert;
    	}

    	$jobs = PublicRepo::getSubscribedJobs(null, $alert);

    	return view('frontend.subscribed-jobs.index', [
    		'jobs' => $jobs,
    		'jobAlert' => PublicRepo::getJobAlert($alert),
    		'isSubscribedJobs' => true
		]);
    }
}
