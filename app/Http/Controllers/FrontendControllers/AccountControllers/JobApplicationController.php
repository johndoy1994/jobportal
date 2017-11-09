<?php

namespace App\Http\Controllers\FrontendControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\FrontendController;
use App\Http\Requests;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends FrontendController
{
    public function __construct() {
    	parent::__construct();
    }

    //job application listing
    public function getIndex() {
    	$user = MyAuth::user();
    	$jobApplications = PublicRepo::jobApplicationsOf($user);
    	return view('frontend.account.job-application.index', [
    		'user'=>$user,
    		'jobApplications' => $jobApplications
		]);
    }

    public function postCancelApplication(Request $request) {
        if($request->has('appId')) {
            $jobApp = PublicRepo::getJobApplication($request->appId);
            if($jobApp) {
                if($jobApp->cancelApplication()) {
                    return redirect()->back()->withSuccessMessage("Application successfully cancelled");
                } else {
                    return redirect()->back()->withErrorMessage("There was an error while cancelling your application, please try again.");
                }
            } else {
                return redirect()->back()->withErrorMessage('Application no longer exits, please try again.');
            }
        } else {
            return redirect()->back()->withErrorMessage('Invalid request to cancel application, please try again.');
        }
    }

}
