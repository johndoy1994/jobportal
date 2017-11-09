<?php

namespace App\Http\Controllers\RecruiterControllers\AccountControllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RecruiterControllers\RecruiterController;
use App\Http\Requests;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PostedJobsController extends RecruiterController
{
    public function getIndex() {
    	$postedJobs=[];
    	if(MyAuth::check('recruiter')) {
            $user = MyAuth::user('recruiter');
           $postedJobs= PublicRepo::getPostedJob($user);
        }
//echo'<pre>';print_r($postedJobs);exit;
      return view('recruiter.account.posted-jobs.index',[
			  'user' => MyAuth::user('recruiter'),
			  'results'=>$postedJobs
    	]);
    }

    public function getApplicationDetais(Request $Request,Job $job){
        $user=[];
        $employer=[];
        $error_message = "You are not Authorize to view this application details!";
        $user = MyAuth::user('recruiter');
        $employer=PublicRepo::getEmployer($user->id);
        if(!$employer){
            return redirect()->back()->with(['error_message' =>$error_message]);
        }else{
            if($job->employer_id!=$employer->id){
                return redirect()->back()->with(['error_message' =>$error_message]);  
            }
        }
        $jobApplication= PublicRepo::getAllJobApplication($job->id);
        //echo'<pre>';print_r($jobApplication);exit;
        return view('recruiter.account.posted-jobs.job-application-details',[
            'user' => $user,
            'results'=>$jobApplication,
            'Jobtitle'=>$job->title,
            'type'=>"application",
            'view'=>'applicant'
        ]);
    }

    public function getApplicationDetailSaved(Request $Request,JobApplication $JobApplication){
       $employer=[];
       $user=[];
       $job=[];
       if(MyAuth::check('recruiter')) {
           $user = MyAuth::user('recruiter');
           $employer = PublicRepo::getEmployer($user->id);
           $job= PublicRepo::getApplicationjob($JobApplication->job_id);
           if($employer && $job && $Request->has('status')){
                if($employer->id!=$job->employer_id){
                    return redirect()->back()->with(['error_message' =>"You are not Authorize to save this application!."]);
                }
           }
        }else{
            return redirect()->back()->with(['error_message' =>"You are not Authorize to save this application!."]);
        }
        $status=$Request->status==0 ? "accepted" : "in-process";
        $saveApplicationDetails = $JobApplication->saveApplicationDetails($JobApplication,$status);
        if($saveApplicationDetails){
            $msg= $Request->status==0 ? "application save successfully" : "application cancle successfully";
            return redirect()->back()->with(['success_message' =>$msg]);
        }else{
            return redirect()->back()->with(['error_message' =>"your application is not saved please try,again!."]);   
        }
    }

    public function getApplicationDetailDelete(Request $Request,JobApplication $JobApplication){
       $employer=[];
       $user=[];
       $job=[];

       if(MyAuth::check('recruiter')) {
           $user = MyAuth::user('recruiter');
           $employer = PublicRepo::getEmployer($user->id);
           $job= PublicRepo::getApplicationjob($JobApplication->job_id);
           if($employer && $job){
                if($employer->id!=$job->employer_id){
                    return redirect()->back()->with(['error_message' =>"You are not Authorize to delete this application!."]);
                }
           }
        }else{
            return redirect()->back()->with(['error_message' =>"You are not Authorize to delete this application!."]);
        }
        
        if($JobApplication->delete()){
            return redirect()->back()->with(['success_message' =>"application delete successfully."]);
        }else{
            return redirect()->back()->with(['error_message' =>"your application is not delete please try,again!."]);   
        }
    }

    public function getActiveInactiveJob(Request $request){
        //print_r($request->action);exit;
        if($request->action=='active'){
           $status=$request->action; 
        }elseif ($request->action=="inactive") {
            $status=$request->action;
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your job status, try again"
            ]);   
        }

        list($status,$message,$jobrec) = AdminRepo::updateJobaction($request->JobId, $status);
        if($status){
           return redirect()->back()->with([
                'success_message' => "Status successfully saved!"
            ]); 
       }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => "There was an error while update your job status, try again"
            ]);
       }
    }

    public function getPostedJobDetais(Request $Request,Job $job){
      
      $user = MyAuth::user('recruiter');

      if($job && $user){
          $employer=PublicRepo::getJobEmployer($job->employer_id);
          if($employer){
            
            if($user->id!=$employer->id){
                return redirect()->route('recruiter-posted-jobs')->withInput(Input::all())->with([
                  'error_message' => "you are not authorize to view this job details"
                ]);    
            }
          }else{
              return redirect()->route('recruiter-posted-jobs')->withInput(Input::all())->with([
                'error_message' => "you are not authorize to view this job details"
              ]);
        }

      }else{
          return redirect()->route('recruiter-posted-jobs')->withInput(Input::all())->with([
                'error_message' => "you are not authorize to view this job details"
          ]);
      }

      $jobDetails=PublicRepo::getPostedJobDetails($job);
      return view('recruiter.account.posted-jobs.job-details',[
            'jobDetails'=>$jobDetails,
            'user' => $user,
        ]);
    }

    //Delete City
    public function getDeletePostedJob(Request $request, Job $job) {

      if($job->delete()) {
      return redirect()->back()->withInput(Input::all())->with([
        'success_message' => "Job successfully deleted!"
      ]);
    } else {
      return redirect()->back()->withInput(Input::all())->with([
        'error_message' => "There was an error while deleting your job, try again!"
      ]);
    }

    }
}
