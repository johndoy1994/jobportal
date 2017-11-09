<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\User;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CandidatesController extends RecruiterController
{
    public function getIndex() {

    	$postedJobs=[];
    	$candidate=[];
    	if(MyAuth::check('recruiter')) {
            $user = MyAuth::user('recruiter');
           $postedJobs= PublicRepo::getPostedJob($user);
        }
        foreach ($postedJobs as $key => $value) {
        	$candidate[$key]= PublicRepo::getAllJobApplication($value['id'],'candidate');
        }

    	return view('recruiter.account.candidates.index',[
			'user' => MyAuth::user('recruiter'),
			'jobs'=>$postedJobs,
			'candidate'=>$candidate,
			'type'=>"candidate",
            'view'=>'candidate'
    	]);
    }

    public function getCandidateDetails(Request $Request, User $UserId){
        if(MyAuth::check('recruiter')) {
            $user = MyAuth::user('recruiter');
            $postedJobs= PublicRepo::getPostedJob($user);
            $job=PublicRepo::getJob($Request->jobId);
            $jobId=($Request->has('jobId')) ? $Request->jobId : '0'; 
            $view=($Request->has('view')) ? $Request->view : null; 

            if($view=='applicant' || $view=='candidate'){
                $view=$view;
            }else{
                return redirect()->route('recruiter-account-home')->withInput(Input::all())->with([
                    'error_message' => "you are not authorize to view this candidate details."
                ]);
            }
            $is_valid= PublicRepo::CheckValidUserToSeeCandidateDetails($UserId->id,$jobId,$user->id);
            if(!$is_valid){
                return redirect()->route('recruiter-candidates')->withInput(Input::all())->with([
                'error_message' => "you are not authorize to view this candidate details."
                ]);    
            }
        }else{
            return redirect()->route('recruiter-candidates')->withInput(Input::all())->with([
                'error_message' => "you are not authorize to view this candidate details."
            ]);
        }
        $Request->id=$Request->jobId;
        $jobDetails=PublicRepo::getPostedJobDetails($Request);
        return view('recruiter.account.candidates.candidate-details',[
            'user' => MyAuth::user('recruiter'),
            'UserData'=>$UserId,
            'Job'=>$job,
            'postedJobs'=>$postedJobs ,
            'jobDetails'=>$jobDetails ,
            'view'=>$view
        ]);
    }
}
