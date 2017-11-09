<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\MyAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobAlertsController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    //job application listing
    public function getIndex() {
    	$user = MyAuth::user();
    	$jobAlerts = $user->job_alerts;
    	return view('frontend.account.job-alerts.index', [
    		'user'=>$user,
    		'jobAlerts' => $jobAlerts
		]);
    }
}
