<?php

namespace App\Http\Controllers\FrontendControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendControllers\JobSearchController;
use App\Http\Requests;
use App\Models\Job;
use App\Models\User;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JobController extends JobSearchController
{

    public function getJob(Request $request, Job $job) {
    	return redirect()->route('job-detail', ['jobId'=>$job->id]);
    }

    public function getApplyJob(Request $request, Job $job) {

    	if($job->isExpired()) {
    		return redirect()->back()->with(['error_message' => "Sorry, job you're trying to apply is expired."]);
    	} else if($job->isEnded()) {
    		return redirect()->back()->with(['error_message' => "Sorry, job you're trying to apply is ended."]);
    	}

        if($job->jobType && $job->jobType->day_selection == 1) {
            if(!$request->has("days")) {
                return redirect()->route('job-detail',['jobId'=>$job->id])->with(['error_message' => "Sorry, you must select your available days before applying to this job."]);
            } else {
                if(!$job->areValidDays($request->days)) {
                    return redirect()->back()->with(['error_message'=>"Selected days has some invalid values, please try again."]);
                }
                // $areDatesValid = Utility::validateDatesInArray($request->days,"Y-m-d", "date_greaterThanToday");
                // if(!$areDatesValid) {
                //     return redirect()->back()->with(['error_message'=>"Selected days has some invalid values, please try again."]);
                // }
            }
        }

    	$applicant_user_id = Session::has("applicant_user_id") ? Session::pull("applicant_user_id") : 0;

        $createNewJobApp = null;

    	if(MyAuth::check()) {
    		if($jobApp=PublicRepo::findJobApplication(MyAuth::user(), $job)) {
    			session()->put('jobApplication', $jobApp);
    			session()->put('alreadyApplied', true);
    		} else {
    			$createNewJobApp = MyAuth::user();
    		}
    	} else if($applicant_user_id > 0) {
    		$createNewJobApp = PublicRepo::getUser($applicant_user_id);
    	} else {
    		$viewfile = "frontend.jobs.apply";
    	}

        if($createNewJobApp) {
            list($success, $message, $jobApp) = PublicRepo::createJobApplication($createNewJobApp, $job, $request->all());
            if($success) {
                $sender  =PublicRepo::getUser($jobApp->user_id);
                $reciever=PublicRepo::getJobEmployer($job->employer_id);
                MessagesRepo::addMessage($sender,['message'=>'apply for "'.$job->title.'" job','receiverId'=>$reciever->id,'file'=>null,'filename'=>null]);
                
                session()->put('jobApplication', $jobApp);
            } else {
                return redirect()->back()->with(['error_message' => "Unable to apply right now, please try again after sometime. [#867312]"]);
            }
        }

    	if(Session::has("jobApplication")) {
    		$alreadyApplied = Session::has('alreadyApplied') ? Session::get('alreadyApplied') : false;
    		$jobApplication = Session::get("jobApplication");
    		Session::put("jobApplication", null);
    		Session::put('alreadyApplied', null);
    		return view("frontend.jobs.application-success",[
    			"jobApplication" => $jobApplication,
    			'alreadyApplied' => $alreadyApplied
			]);
    	} else {
    		return view($viewfile, [
	    		'job' => $job,
	    		'applicant_user_id' => $applicant_user_id,
                'jobDays' => $request->days
			]);
    	}

    }

    public function postApplyAsGuest(Request $request, Job $job) {

    	if($job->isExpired()) {
    		return redirect()->back()->with(['error_message' => "Sorry, job you're trying to apply is expired."]);
    	} else if($job->isEnded()) {
    		return redirect()->back()->with(['error_message' => "Sorry, job you're trying to apply is ended."]);
    	}

        if($job->jobType && $job->jobType->day_selection == 1) {
            if(!$request->has("days")) {
                return redirect()->back()->with(['error_message' => "Sorry, you must select your available days before applying to this job."]);
            } else {
                if(!$job->areValidDays($request->days)) {
                    return redirect()->back()->with(['error_message'=>"Selected days has some invalid values, please try again."]);
                }
                // $areDatesValid = Utility::validateDatesInArray($request->days, "Y-m-d", "date_greaterThanToday");
                // if(!$areDatesValid) {
                //     return redirect()->back()->with(['error_message'=>"Selected days has some invalid values, please try again."]);
                // }
            }
        }

    	$this->validate($request, [
    		//'email_address' => 'required|email|unique:users',
            'email_address' => 'required|email',
    		'name' => 'required',
    		'cv' => 'mimes:txt,rtf,pdf,docx,doc,zip'
		],[
			//'email_address.unique' => "Sorry, this e-mail address is already registered with us, try again.",
			'cv.mimes' => "Upload failed: Only file type: txt, rtf, doc, docx and pdf allowed, Please try again"
		]);
        
        $email_address = $request->email_address;
    	$name = $request->name;

        $UserData=PublicRepo::checkUserEmailIsExits($email_address);
        if($UserData){
            if($UserData->status=='ACTIVATED'){
                return redirect()->back()->with(['is_olsUser'=> true,'error_message'=>"Sorry, this e-mail address is already registered with us, please sign in to continue the application process."]);             
            }else{
                return redirect()->back()->with(['error_message'=>"Sorry, this e-mail address is already registered with us, please check your email for sign-in."]);
            }
            
        }

    	$user = PublicRepo::findOrCreateUser($email_address, $name);
    	if($user)  {

    		if($request->file("cv")) {
    			list($status, $message) = PublicRepo::uploadResume($user, $request->file("cv"));
    			if($status) {

    			} else {
    				return redirect()->back()->with(['error_message'=>"There was an error while uploading your resume, please try again."]);
    			}
    		}

    		list($success, $message, $jobApp) = PublicRepo::createJobApplication($user, $job, $request->all());
    		if($success) {
                $sender  =PublicRepo::getUser($jobApp->user_id);
                $reciever=PublicRepo::getJobEmployer($job->employer_id);
                MessagesRepo::addMessage($sender,['message'=>'apply for "'.$job->title.'" job','receiverId'=>$reciever->id,'file'=>null,'filename'=>null]);
                $jobApp->is_guest = 1;
                $jobApp->update();
    			return redirect()->back()->with([
	    			'jobApplication' => $jobApp
				]);
    		} else {
    			return redirect()->back()->with(['error_message'=> "Unable to apply right now, please try again after sometime."]);
    		}
    	} else {
    		return redirect()->back()->with(['error_message' => "Apply as guest is failed, try again"]);
    	}

    }

}
