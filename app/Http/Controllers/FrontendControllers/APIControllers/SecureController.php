<?php

namespace App\Http\Controllers\FrontendControllers\APIControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\JobApplication;
use App\Models\Messages;
use App\Models\User;
use App\Models\UserResume;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Repos\UserRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class SecureController extends Controller
{
	public function postAutoChat(Request $request) {
		if($request->has('type') && $request->has('ref')) {
			$type = $request->type;
			$ref = $request->ref;
			$user = MyAuth::getUser($type);
			if($user) {
				$conversationMessages = MessagesRepo::getConversationMessages($ref, $user,0,false,true);
				return view('templates.messages.conversation', [
					'conversationMessages' => $conversationMessages
				]);
			}
		}
	}

	public function postUpdateJobApplicationDates(Request $request) {
		$json = ["success"=>false, "message"=>"Invalid request, please try again"];

		if($request->has('appId') && $request->has('dateString')) {
			$appId = $request->appId;
			$dateString = $request->dateString;

			$jobApp = PublicRepo::getJobApplication($appId);
			if($jobApp) {
				if(Utility::validateDatesInArray($dateString,"Y-m-d","date_greaterThanToday")) {
					$appMeta = $jobApp->meta;
					$appMetaJson = json_decode($appMeta, true);
					if(!is_array($appMetaJson)) {
						$appMetaJson = [];
					}

					$allValid = true;
					$days = [];
					foreach($dateString as $invalid_date) {
						if($valid_date = Utility::parseCarbonDate($invalid_date, "Y-m-d")) {
							if($jobApp->job->hasDateInDays($valid_date->format("Y-m-d"))) {
								$days[] = $valid_date->format("Y-m-d");
							} else {
								$allValid=false;
								$json["message"] = "Selected date (".$valid_date->format('d-m-Y').") doesn't belong to selected application, try again.";
								break;
							}
						}
					}

					$appMetaJson["days"] = $days;

					if($allValid) {
						$jobApp->meta = json_encode($appMetaJson);

						if($jobApp->update()) {
							$json["success"] = true;
							$json["message"] = "Saved";
						} else {
							$json["message"] = "Unable to update job application now, try again.";
						}
					}	

				} else {
					$json["message"] = "One of selected dates is invalid or passed, try again.";
				}
			} else {
				$json["message"] = "Job application not found, try again.";
			}

		}

		return response()->json($json);
	}

	public function postShowJobApplication(Request $request) {
		$jobApp = PublicRepo::getJobApplication($request->jobApp);
		return view('backend.job-application-detail', [
			"app" => $jobApp
		]);
	}

	public function postUpdateEmailAddress(Request $request){
		$json = [false , "Invalid request"];
		$user=null;
		if(MyAuth::check()){
			$user=MyAuth::user();	
		}else{
			$json = [false , "User session expire"];	
		}
		
		if($request->has('email') && $user) {
			$json = UserRepo::updateUser($user,['email_address'=>$request['email']]);
		}
		return response()->json($json);
	}

	public function postNewMessage(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::getUser($request->type);
		$filename=null;
		if ($request->hasFile('file')) {
		    $_filename=uniqid(rand(10,100)).'.'.$request->file('file')->getClientOriginalExtension();
		    $result=Storage::put(
	        	'message-attachments/'.$_filename,file_get_contents($request->file('file')->getRealPath())
	        );
	        if($result){
	        	$filename=$_filename;
	        }
	    }

		if($request->has('message') && $request->has('receiverId') && $user) {
			$is_message=0;
			if($request->has('is_message')){
				$is_message=($request->is_message==1) ? 1 : 0;
			}
			list($status[0],$conversationMessages[0]) = MessagesRepo::addMessage($user,['message'=>$request['message'],'receiverId'=>$request['receiverId'],'file'=>$request->file('file'),'filename'=>$filename],$is_message);
			if($is_message==0){
				if($status[0]){
					$data=View::make('templates.messages.conversation',['conversationMessages'=>$conversationMessages[0],'type'=>$request->type]);
					$json = [true, $data->render(), $conversationMessages[0][0]["msgObject"]->id];
				}else{
					$json = [false , "some thing want to wrong please try again"];
				}
			}else{
				if($status[0]){
					$json = [true,"true"];
				}else{
					$json = [false,"message not send successfully,try again."];
				}	
			}
		}

		return response()->json($json);
	}

	public function postMultipleNewMessage(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::getUser($request->type);
		$filename=null;
		if ($request->hasFile('file')) {
		    $_filename=uniqid(rand(10,100)).'.'.$request->file('file')->getClientOriginalExtension();
		    $result=Storage::put(
	        	'message-attachments/'.$_filename,file_get_contents($request->file('file')->getRealPath())
	        );
	        if($result){
	        	$filename=$_filename;
	        }
	    }
		$data=explode(",",str_replace('multiselect-all,', '', $request->receiverId));
		$data=array_filter($data);
		
		if(in_array('jobseeker', $data) || in_array('employer', $data)){
			if(in_array('jobseeker', $data) && in_array('employer', $data))
			{
				$data=User::where('level', '!=', 'BACKEND_ADMIN')->get();
			}elseif(in_array('jobseeker', $data))
			{
				$data=User::where('type', '=', 'JOB_SEEKER')->get();
			}elseif(in_array('employer', $data))
			{
				$data=User::where('type', '=', 'EMPLOYER')->get();
			}	
			if(!empty($data)){
				foreach ($data  as $value) {

					if($request->has('message') && $user) {
					$is_message=0;
					if($request->has('is_message')){
						$is_message=($request->is_message==1) ? 1 : 0;
					}
					list($status[0],$conversationMessages[0]) = MessagesRepo::addMessage($user,['message'=>$request['message'],'receiverId'=>$value->id,'file'=>$request->file('file'),'filename'=>$filename],$is_message);
					$json = [true , "send successfully"];
					}	
				}
			}else{
				$json = [false , "message not send successfully."];
			}

		}else{
			
			if(!empty($data)){
				foreach ($data  as $value) {
					if($request->has('message') && $user) {
					$is_message=0;
					if($request->has('is_message')){
						$is_message=($request->is_message==1) ? 1 : 0;
					}
					list($status[0],$conversationMessages[0]) = MessagesRepo::addMessage($user,['message'=>$request['message'],'receiverId'=>$value,'file'=>$request->file('file'),'filename'=>$filename],$is_message);
					$json = [true , "send successfully"];
					}	
				}
			}else{
				$json = [false , "message not send successfully."];
			}
		}
		

		return response()->json($json);
	}

	public function postMessageStatusUpdate(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::getUser($request->type);
		if($user && $request->has('conversation_ref')){
			$json = MessagesRepo::MessageStatusUpdate($request->conversation_ref,$user);
		}else{
			$json = [false , "Some thing want to wrong please try again"];
		}

		return response()->json($json);
	}

	public function postNotificationStatusUpdate(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::getUser($request->type);
		if($user && $request->has('UserId') && ($request->UserId==$user->id)){
			$json = MessagesRepo::notificationStatusUpdate($request->UserId,$user);
		}else{
			$json = [false , "Some thing want to wrong please try again"];
		}

		return response()->json($json);	
	}

	public function postSendEmailApplication(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::user('recruiter');
		
		if($request->has('subject') && $request->has('content') && $request->has('applicationId') && $user) {
			$applicantUser=PublicRepo::getUserData($request->applicationId);
			$recruiterData=PublicRepo::getJobEmployerOf($request->applicationId);
			
			if($recruiterData && $applicantUser){
				if($recruiterData->user_id==$user->id){
					Notifier::recruiterSendMailToApplicant($applicantUser,$request->content,$request->subject);
					$json = [true , "email send successfully."];
				}else{
					$json = [false , "You are not authorize to send email"];
				}
			}else{
				$json = [false , "You are not authorize to send email1"];
			}
		}

		return response()->json($json);
	}

	public function postSendEmailMultiuser(Request $request){
		$json = [false , "Invalid request"];
		$user=null;
		if($request->has('login')){
			$user = MyAuth::user($request->login);
		}
		if($user){
			$data=explode(",",str_replace('multiselect-all,', '', $request->receiverId));
			$data=array_filter($data);
			
			if(in_array('jobseeker', $data) || in_array('employer', $data)){
				if(in_array('jobseeker', $data) && in_array('employer', $data))
				{
					$data=User::where('level', '!=', 'BACKEND_ADMIN')->get();
				}elseif(in_array('jobseeker', $data))
				{
					$data=User::where('type', '=', 'JOB_SEEKER')->get();
				}elseif(in_array('employer', $data))
				{
					$data=User::where('type', '=', 'EMPLOYER')->get();
				}	
				if(!empty($data)){
					$userEmails = [];
					foreach ($data  as $value) {
						$reciever=PublicRepo::getUser($value->id);
						$userEmails[] = $reciever->email_address;
					}

					if($request->has('subject') && $request->has('content') && $user) {
						Notifier::MultiUserSendNotification($userEmails,$request->subject,$request->content,$user);
						$json = [true , "email send successfully."];		
					}
					
				}else{
					$json = [false , "message not send successfully."];
				}

			}else{
				if(!empty($data)){
					$table=($request->has('table'))? "user_contacts" : "users" ;
					$userEmails = [];
					foreach ($data  as $value) {
						if($table=='user_contacts'){
							$reciever=PublicRepo::getUsercontact($value);
							$userEmails[] = $reciever->email;
						}else{
							$reciever=PublicRepo::getUser($value);
							$userEmails[] = $reciever->email_address;
						}
					}

					if($request->has('subject') && $request->has('content') && $user) {
						Notifier::MultiUserSendNotification($userEmails,$request->subject,$request->content,$user);
						$json = [true , "email send successfully."];		
					}
				}else{
					$json = [false , "email not send successfully."];
				}
			}
		}else{
			$json = [false , "You are not authorize to send email."];
		}

		return response()->json($json);
	}

	public function getJobMatchStatus(Request $request){
		$json = [false , "Invalid request"];
		$user = MyAuth::user('recruiter');
		$is_valideJob=false;
		$is_valideApplicant=false;
		$data='You are not authorize to view this candidate match status';
		if($request->has('JobId') && $request->has('UserId') && $user) {
			$job=PublicRepo::getJob($request->JobId);
			$userData=PublicRepo::getUser($request->UserId);
			if($job){
				$is_valideJob=PublicRepo::getPostedJobDetails($job,$user->id);
			}
			$is_valideApplicant=PublicRepo::getApplicantApply($request->UserId);
			if($is_valideJob && $is_valideApplicant){
				$data=View::make('templates.job-match-status.index',['job'=>$job,'user'=>$userData]);
				$json = [true, $data->render()];
			}	
		}
		if($is_valideJob && $is_valideApplicant){
			echo $data->render();
		}else{
			echo $data;
		}	
	}
	
}