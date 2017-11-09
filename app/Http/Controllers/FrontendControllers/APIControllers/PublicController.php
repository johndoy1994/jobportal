<?php

namespace App\Http\Controllers\FrontendControllers\APIControllers;

use App\Helpers\Notifier;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\City;
use App\Models\CmsPage;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\User;
use App\Models\UserResume;
use App\MyAuth;
use App\Repos\API\MessagesRepo;
use App\Repos\API\PublicRepo;
use App\Repos\AdminRepo;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Laravel\Socialite\Facades\Socialite;

class PublicController extends Controller
{

	///////////////////////
	/// Through Service ///
	///////////////////////

	public function getThrough(Request $request, $provider) {
        
        if($request->has("action")) {
            session()->put('through-action', $request->action);
            session()->put('through-action-data', $request->all());
        } else {
            if(session("through-action")) {
                Session::forgot('through-action');
                Session::forgot('through-action-data');
            }
        }

        // New redirect uri...
        $config = config('services.'.$provider);
        $config["redirect"] = env('APP_URL')."/api/through/$provider/callback";
        config(['services.'.$provider => $config]);

        switch($provider) {
            case "facebook":
                return Socialite::driver($provider)->scopes(['public_profile','email','user_friends'])->redirect();
            break;

            case "google":
            	return Socialite::driver($provider)->scopes(['openid','profile','email','https://www.googleapis.com/auth/contacts.readonly'])->redirect();
            break;

            default:
                return Socialite::driver($provider)->redirect();
            break;
        }

	}

	public function getThroughCallback(Request $request, $provider) {

        if($request->has('error')) {
            // error
        }

        $config = config('services.'.$provider);
        $config["redirect"] = env('APP_URL')."/api/through/$provider/callback";
        config(['services.'.$provider => $config]);

        //$provider = $provider."Login";
        $user = null;

        try {

            $user = Socialite::driver($provider)->user();
            $token = $user->token;
            // echo '<pre>';
            // print_r($user);
            // echo '</pre>';
            // echo '<hr/>';

            $action = "login";
            $actionData = [];

            if(Session::has('through-action')) {
            	$action = session()->get('through-action');
            	$actionData = session()->get('through-action-data');
            }

            switch ($provider.'.'.$action) {
            	case 'google.contacts':
            			$returnRoute = "front-home";
            			if(isset($actionData['redirect'])) {
            				$returnRoute = $actionData["redirect"];
            			}
            			$link = "https://www.google.com/m8/feeds/contacts/default/full?max-results=1000&alt=json&v=3.0&oauth_token=$token";
            			$linkData = file_get_contents($link);

            			$contacts = json_decode($linkData,true);
            			$return = array();
						if (!empty($contacts['feed']['entry'])) {
							foreach($contacts['feed']['entry'] as $contact) {
					           //retrieve Name and email address  
								// $return[] = array (
								// 	'name'=> strlen($contact['title']['$t']) == 0 ? 'No name' : $contact['title']['$t'],
								// 	'email' => $contact['gd$email'][0]['address'],
								// );
								 if(isset($contact['gd$email'])) {
									$return[] = array (
										'name'=> strlen($contact['title']['$t']) == 0 ? 'No name' : $contact['title']['$t'],
										'email' => $contact['gd$email'][0]['address'],
									);	
								}
							}				
						}
						return redirect()->route($returnRoute)->with(['googleContacts'=>$return]);
            		break;
            	
            	default:
            		# code...
            		break;
            }


        } catch(InvalidStateException $exception) {

        }



	}

	///////////////////////

	public function getJobCategories(Request $request) {

		$json = [];
		if($request->has("q")) {
			$json = PublicRepo::searchJobCategories($request['q'], $request->limit);
		} else {
			$json = PublicRepo::allJobCategories();
		}

		return response()->json($json);

	}

	public function postResetPasswordLink(Request $request) {

		$this->validate($request, [
			'password' => 'required|confirmed|min:5',
			'password_confirmation' => 'required|min:5',
			'code' => 'required'
		]);

		list($success, $message) = PublicRepo::setNewPassword($request->code, $request->password);

		if($success) {
			return redirect()->back()->withSuccessMessage($message)->with(["hideForm"=>true]);
		} else {
			return redirect()->back()->withErrorMessage($message);
		}

	}

	public function getResetPasswordLink(Request $request) {
		$code = $request->has('code') ? $request->code : "";
		$type="account-forgotpassword";
		
		if($request->has('type')){
			if($request->type==3){
				$routeType="admin-home";
			}elseif ($request->type==1) {
				$routeType="account-forgotpassword";
			}else{
				$routeType="recruiter-account-forgotpassword";
			}
			$type = $routeType;//($request->type==1) ? "account-forgotpassword" : "recruiter-account-forgotpassword";
		} 
		$passwordReset = PublicRepo::passwordResetOf($code);
		return view("reset-password-form", [
			'passwordReset' => $passwordReset,
			'code' => $code,
			'type' =>$type
		]);
	}

	public function getResetPassword(Request $request) {
		$json = ["success"=>false, "message"=>"Invalid request, try again."];

		if(!$request->has("email_address") && !$request->has("mobile_number")) {
			$user = null;
			$notification_type = "";
			if($request->has("email_address")) {
				$user = PublicRepo::findUser($request->email_address);
				$notification_type = "mail";
			} else if($request->has("mobile_number")) {
				$user = PublicRepo::findUser("NOEMAIL", $request->mobile_number);
				$notification_type = "sms";
			} else {
				$json["message"] = "There was an error while resetting your password, please try again.";
			}

			if($user) {	
				if(PublicRepo::generateResetPasswordToken($user, $notification_type)) {
					$json["success"] = true;
					if($notification_type == "mail") {
						$json["message"] = "In a very short time, you will receive mail from us with link to reset your password, simply follow the link to reset your password.";
					} else if($notification_type == "sms") {
						$json["message"] = "In a very short time, you will receive PIN to reset your password.";
					} else {
						$json["message"] = "In a very short time, you will receive link to reset your password";
					}
				} else {
					$json["message"] = "Sorry, failed to send you an reset password link, please try again.";
				}
			} else {
				$json["message"] = "User not found, please try again.";
			}

		}

		return response()->json($json);
	}

	public function getJobDays(Request $request) {
		$json = ['success'=>false, 'message'=>"Invalid request, please try again."];
		
		$reqJobId = $request->has('job') ? $request->job : 0;		
		$job = PublicRepo::getJob($reqJobId);
		if($job) {
			$meta = $job->meta;
			$jMeta = json_decode($meta, true);
			if(is_array($jMeta)) {
				if(isset($jMeta["days"])) {
					$json["success"] = true;
					$json["message"] = "Please select your available days by clicking particular day.";
					$json["apply_uri"] = route('frontend-job-apply', ['job'=>$job->id]);
					$days = [];
					$defaultDate = \Carbon\Carbon::now();
					foreach ($jMeta["days"] as $date) {
						if($pDate = Utility::parseCarbonDate($date,"Y-m-d")) {
							if($defaultDate<$pDate) {
								$defaultDate=$pDate;
							}
							if(Utility::date_greaterThanToday($pDate)) {
								$days[] = $pDate->format('Y-m-d');
							}
						}
					}
					if(count($days)<=0) {
						$json["message"] = "Sorry, no available days.";
					}
					$json["days"] = $days;
					$json["defaultDate"] = $defaultDate->format("Y-m-d");
				} else {
					$json["message"] = "Job didn't have any preset days, please contact job advertiser.";
				}
			} else {
				$json["message"] = "There was an error while fetching job meta data, please try again.";	
			}
		} else {
			$json["message"] = "Job not found";
		}
		
		return response()->json($json);
	}

	public function postModifyProfile(Request $request) {
		$json = ['success'=>false, 'message'=>"Invalid request, please try again"];

		if($request->has('target') && $request->has('job')) {
			$target = $request->target;
			$jobId = $request->job;
			$data = $request->data;

			if(MyAuth::check()) {
				$job = PublicRepo::getJob($jobId);
				if($job) {
					list($success, $message) = PublicRepo::matchProfile(MyAuth::user(), $target, $job, $data);
					$json["success"] = $success;
					$json["message"] = $message;
					if($success && $target=="job_title_id") {
						$json["show_all"] = true;
					}
				} else {
					$json["message"] = "Job not found";
				}
			} else {
				$json["message"] = "User not authorized, please try again.";
			}
		}

		return response()->json($json);
	}

	public function postSaveJobAlertContentType(Request $request) {
		$json = ['success'=>false, 'message'=>"Invalid request, try again"];

		if($request->has("alert_content_type")) {
			list($success, $message) = PublicRepo::saveJobAlertContentType($request->alert_content_type, MyAuth::user());
			$json["success"] = $success;
			$json["message"] = $message;
		}

		return response()->json($json);
	}

	public function getCreateAlert(Request $request) {
		$json = ['success'=>false, 'message'=>"Invalid request to create alert, try again"];

		$this->validate($request, [
			'email_address' => 'required|email',
			'job_categories_id'=> 'required|integer',
            'job_title_id'=>'required|integer',
            'city'=>'required|integer',
            'radius'=>'required|integer'
		]);

		if($request->has("email_address")) {

			$email_address = $request->email_address;

			$job_categories_id = $request->has('job_categories_id') ? $request->job_categories_id : "";
			$job_title_id = $request->has('job_title_id') ? $request->job_title_id : "";
			$keywords = $request->has('keywords') ? $request->keywords : "";
			$radius = $request->has('radius') ? $request->radius : 0;
			$city_id = $request->has('city') ? $request->city : 0;
			$salary_type_id = $request->has('salary_type_id') ? $request->salary_type_id : 0;
			$salary_range_from = $request->has('salary_range_from') ? $request->salary_range_from : 0;
			$job_type_id = $request->has('job_type_id') ? $request->job_type_id : 0;
			$industries_id = $request->has('industries_id') ? $request->industries_id : 0;

			list($success, $message) = PublicRepo::createAlert($email_address, [
				'keywords' => $keywords,
				'job_categories_id' => $job_categories_id,
				'job_title_id' => $job_title_id,
				'radius' => $radius,
				'city_id' => $city_id,
				'salary_type_id' => $salary_type_id,
				'salary_range_from' => $salary_range_from,
				'job_type_id' => $job_type_id,
				'industries_id' => $industries_id
			]);
			
			$json["success"] = $success;
			$json["message"] = $message;
		}

		return response()->json($json);
	}

	public function getSaveJob(Request $request, Job $job) {
		list($status, $message) = PublicRepo::processSaveJob($job, $request->has('remove'));
		return response()->json(['success'=>$status, 'message'=>$message]);
	}

	public function getJobs(Request $request) {
		$json = [];

		if($request->has("marker_ids")) {

			$jobs = Job::find($request->marker_ids);

			$route_params = $request->all();

			return view('includes.frontend.job-search.marker_view', [
				'jobs' => $jobs,
				'route_params' => $route_params
			]);

		}

		return response()->json($json);
	}

	public function getCVs(Request $request) {
		$json = [];

		if($request->has("marker_ids")) {

			$cvs = PublicRepo::fetchJobSeekers($request->marker_ids);

			$filters = $request->all();

			return view('includes.recruiter.search-cv.marker_view', [
				'cvs' => $cvs,
				'filters' => $filters
			]);

		}

		return response()->json($json);	
	}

	public function getLocations(Request $request) {
		$json = [];

		$limit = $request->has("limit") ? $request->limit : 0;

		if($request->has('q')) {	
			$json = PublicRepo::searchLocations($request->q, 0, null, $limit);
		} else {
			$json = PublicRepo::allLocations(0, $limit);
		}

		return response()->json($json);
	}

	public function getJobKeywords(Request $request) {
		$json = [];
		$keyword = "";
    	$location = "";
    	$radius = 0;
    	$salaryType = 0;
    	$salaryRate = 0;
    	$daysAgo = 0;
    	$recruiterType = 0;
    	$jobType = 0;
        $jobCategory = 0;
        $loggedInUserId = '';
        $cookieJobSearchData = array();
        $jobCategoryName=null;
        $jobTypeName=null;
        $recruiterTypeName=null;
        $salaryTypeName=null;
        $radius_data=false;
		if($request->has('jobId')) {
			$json = PublicRepo::jobKeywordsOf($request->jobId);
		} else if($request->has('q')) {
			//$json = PublicRepo::searchJobKeywords($request->q);
			$json = PublicRepo::searchJobKeywords($request->q);
		} else {
			$json = PublicRepo::allJobKeywords();
		}

		return response()->json($json);
	}

	public function getSkills(Request $request) {
		$json = [];

		if($request->has('jobTitleId')) {
			$json = PublicRepo::skillsOf($request["jobTitleId"]);
		} else if($request->has('q')) {
			$json = PublicRepo::searchSkills($request["q"]);
		} else {
			$json = PublicRepo::allSkills();
		}

		return response()->json($json);
	}

	public function getJobTitles(Request $request) {
		$json = [];
		$query = null;
		if($request->has("q"))
			$query = $request["q"];

		if($request->has('jobCategoryId')) {
			$json = PublicRepo::jobTitlesOf($request["jobCategoryId"], $query);

		} else {
			$json = PublicRepo::allJobTitles();
		}

		return response()->json($json);
	}

	public function getSalaryRanges(Request $request) {

		$json = [];

		if($request->has('salaryTypeId')) {
			$json = PublicRepo::salaryRangeOf($request["salaryTypeId"]);
		}

		return response()->json($json);
	}

	public function getCertificates(Request $request) {
		$json = PublicRepo::allCertificates();
		return response()->json($json);
	}

	public function getCountries(Request $request) {

		$json = [];

		if($request->has('q')) {
			$json = PublicRepo::searchCountries($request->q,$request->limit,0);
		} else {
			$json = PublicRepo::allCountries(0);
		}

		return response()->json($json);

	}

	public function getStates(Request $request) {

		$json = [];
		$query = null;
		$limit=0;
		if($request->has('q')){
			$query = $request->q;
			$limit=1;
		}

		if($request->has('countryId')) {
			$json = PublicRepo::searchStates($request["countryId"], $query ,$limit); // By Pratik
			//$json = PublicRepo::statesOf($request["countryId"],0, $query);
		} else {
			$json = PublicRepo::allStates(0);
		}

		return response()->json($json);

	}

	public function getCities(Request $request) {

		$json = [];
		$query = null;
		$limit=0;
		if($request->has('q')){
			$query = $request->q;
			$limit=1;
		}

		if($request->has('stateId')) {
			$json = PublicRepo::searchCities($request["stateId"], $query, $limit); // By Pratik
			//$json = PublicRepo::citiesOf($request["stateId"],0);
		} else {
			$json = PublicRepo::allCities(0);
		}

		return response()->json($json);

	}

	public function getRecordPerPage(Request $request) {
		$json = ['success'=>false, 'message'=>"Invalid request, try again"];

		if($request->has("target") && $request->has("value")) {
			Controller::recordsPerPage($request->target, $request->value);
			$json["success"] = true;
		}

		return response()->json($json);
	}

	public function getCVDownload(Request $request,User $id){
        $resume = UserResume::where('user_id', $id->id)->first();
        if($resume){
            $file = storage_path().'/app/resumes/'.$resume->filename;
            return Response::download($file, $id->getName()."_".$resume->filename, ['content-type' => $resume->mime]);        
        }else{
            return redirect()->back()->withInput(Input::all())->with([
                'error_message' => 'User CV is not available.'
            ]);
        }
    }

    public function postJobApplicationStatusChange(Request $request){
		list($status,$message) = [false , "Invalid request"];
		if($request->has('id') && $request->has('status')){
			list($status,$message) = AdminRepo::ApplicationStatusUpdate($request->id,$request->status);
		}else{
			list($status,$message) = [false , "some thing want to wrong please try again"];
		}

		return response()->json($status);	
	}

	public function expireJobsAutosetInactive(){
		$Jobs=PublicRepo::getallJobs();
		$todayDate = \Carbon\Carbon::now()->format('Y-m-d');
		foreach ($Jobs as $value) {
			$isEnded=0;
			if($value->ending_date){
			 	$isEnded=Utility::diffInDates($todayDate, $value->ending_date->format('Y-m-d'));
			}
			$isexpired=Utility::diffInDates($todayDate, $value->expiration_date->format('Y-m-d'));
			if($isEnded<0 || $isexpired<0){
			 	PublicRepo::updateJobStatus($value->id);
			}

		}
	}

	public function getViewCmsPage(Request $request)
	{
		$pageId = $request->get('pageId');
		$cmsPageData = CmsPage::where('id',$pageId)->first();
		return view("frontend.cms-page-template", [
			'cmsPageData' => $cmsPageData
		]);
	}

	public function jobAlert(){
		//$jobs=PublicRepo::searchJobs();
		$keyword = "";
    	$location = "";
    	$radius = 0;
    	$salaryType = 0;
    	$salaryRate = 0;
    	$daysAgo = 0;
    	$recruiterType = 0;
    	$jobType = 0;
        $sortBy = "";
        $perPage=0;
        $filters = [];
		list($jobs, $location_cordinates, $clearAddress) = PublicRepo::searchJobs($keyword, $location, $radius, $salaryType, $salaryRate, $daysAgo, $recruiterType, $jobType, $sortBy, $perPage, $filters);

		$to_emailAll = array();
		$sendJobAlerttoUser=[];
		$multi_array=[];
		foreach ($jobs as $value) {
			$data=PublicRepo::searchJobAlerts($value->getCategoryId(), $value->job_title_id, $keywords="", $radius=0, $value->getCityId(), $value->salary_type_id, $value->salary_range_id, $value->job_type_id, $industries_id=0, $value->title, $value->getJobCoordinates());
			
			$to_email = array();
			foreach ($data as $userEmail) 
			{
				if(!in_array($value->id,explode(",", $userEmail->jobAlerted))){
					$multi_array[$userEmail->email_address][]=$value;
					$sendJobAlerttoUser[$userEmail->email_address][]=$value->id;
				}	
				
			}
			
		}

		foreach ($multi_array as $key => $value) {
			Notifier::jobAlert($key,$value);
			PublicRepo::UpdateUserAlertJobStatus($key,$sendJobAlerttoUser[$key]);
		}
	}

	public function getcityLatlong(){
		$q =City::select('countries.name as country_name','states.name as state_name','cities.*')
		->join('states', 'states.id', "=", "cities.state_id")
		->join('countries', 'countries.id', "=", "states.country_id")
		->where('cities.update_status','=',0)
		->where('countries.id','!=',233)
		->orderBy('countries.id')
		->get();
		
		foreach ($q as $key => $value) {
			
			list($valid_location, $location_point, $clearAddress)=PublicRepo::getGeoLocationPoint($value->name.", ".$value->state_name.", ".$value->country_name);
			if($valid_location){
				$City = City::where('id', $value->id)->first();
				if($City){
					$City->latitude = $location_point[0];
					$City->longitude = $location_point[1];
					$City->update_status = 1;
					if($City->update()){
						
					}	
				}	

			}
		}
	}

	public function getNotification(Request $Request){
		$user=MyAuth::user('admin');
        //echo strlen($user->id);exit;
        if($Request->has('reciever') && $Request->has('conversationref')){
        	$reciever=PublicRepo::getUser($Request->reciever);
        	if($reciever){
        		$refNumber=MessagesRepo::makeConversationRef([$reciever->id,$user->id]);
	        		if($refNumber!=$Request->conversationref){
	        			echo "You are not Authorize to View this Conversation";
	        		// 	return redirect()->route('admin-home')->with([
	        		// 	'error_message' => "You are not Authorize to View this Conversation"
	        		// ]);
        		}	
        	}else{
        		echo "You are not Authorize to View this Conversation";
        		// return redirect()->route('admin-home')->with([
        		// 	'error_message' => "You are not Authorize to View this Conversation"
        		// ]);
        	}
        }else{
        	echo "You are not Authorize to View this Conversation";
   //      	return redirect()->route('admin-home')->with([
			// 	'error_message' => "You are not Authorize to View this Conversation"
			// ]);
        }
        
    	
        //$myConversations = MessagesRepo::getConversationRef($user);
        if($Request->has('Usertype') && $Request->has('reciever') && ($Request->Usertype=='jobseeker' || $Request->Usertype=='recruiter')){
        	$myConversations = PublicRepo::getJobSeekerOrRecruiter($Request->Usertype,$search="",$is_paging=false);
        	$reciever=PublicRepo::getUser($Request->reciever);
        	if($reciever) {
    			$conversationTitle = $reciever->name;
    			$conversationId = $reciever->id;
    		}
        }else{
        	echo "You are not Authorize to View this Conversation";
   //      	return redirect()->route('admin-home')->with([
			// 	'error_message' => "You are not Authorize to View this Conversation"
			// ]);	
        }	
    	
        	
        foreach ($myConversations as $key=>$val) {
            	$myConversations[$key]->conversation_ref=MessagesRepo::makeConversationRef([$val->id,$user->id]);
        } 
        $conversationMessages = MessagesRepo::getConversationMessages($Request->conversationref, $user,$lastMessageId=0,$lastmessage=false,$onlyNew = true,$is_message=1);
    	
		$data=View::make('templates.notification.notification_temp',[
			'user'=>$user,
			'conversation_ref'=>$Request->conversationref,
			'conversationTitle' => $conversationTitle,
    		'conversationId' => $conversationId,
    		'myConversations' => $myConversations,
    		'conversationMessages' => $conversationMessages,
            'type'=>3,
            'Usertype'=>$Request->Usertype
			]);
		echo $data->render();
	}
}
