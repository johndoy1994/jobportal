<?php

namespace App\Repos;

use App\Helpers\Notifier;
use App\Models\CmsPage;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobAddress;
use App\Models\JobApplication;
use App\Models\JobCertificate;
use App\Models\JobKeyword;
use App\Models\JobSkill;
use App\Models\JobWeekday;
use App\Models\User;
use App\Models\UserSkill;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Repos\GeneralRepo;
use App\Repos\UserRepo;
use App\Utility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminRepo extends Repo {

	public static function addJob($data) {

		if($data['salary_check']==1){
			$salary=$data["salary"];
		}else{
			$salary=0;
		}
		if(empty($data["ending_date"])){
			$end_date=NULL;
		}else{
			$end_date=\Carbon\Carbon::parse($data["ending_date"])->format('Y-m-d');
		}
		$job = new Job();
		$job->employer_id = $data["employer_id"];
		$job->title = $data["title"];
		$job->vacancies = $data["vacancies"];
		$job->job_title_id = $data["job_title_id"];
		$job->education_id = $data["education_id"];
		$job->experience_id = $data["experience_id"];
		$job->job_type_id = $data["job_type_id"];
		$job->experience_level_id = $data["experience_level_id"];
		$job->starting_date = \Carbon\Carbon::parse($data["starting_date"])->format('Y-m-d');
		$job->ending_date = $end_date;
		$job->work_schedule_from = $data["work_schedule_from"];
		$job->work_schedule_to = $data["work_schedule_to"];
		$job->salary_type_id = $data["salary_type_id"];
		$job->salary = $salary;
		$job->pay_by_id = $data["pay_by_id"];
		$job->pay_period_id = $data["pay_period_id"];
		$job->benefits = $data["benefits"];
		$job->description = $data["description"];
		$job->expiration_date = \Carbon\Carbon::parse($data["expiration_date"])->format('Y-m-d');
		$job->status ="active";
		$job->renew_date = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
		//$job->salary_range_id =$data["salary_range_id"];

		$metaArray = ["days"=>isset($data["dates"]) ? $data["dates"] : []];
		$job->meta = json_encode($metaArray);

		if($job->save()){
			Notifier::jobPosted($job,MyAuth::user('admin'));
			return [true, "Saved", $job];
		} else {
			return [false, "Not saved", null];
		}
	}

	public static function addJobCertificates($jobrec, $certificates){
		//echo $jobrec->id."<br/>";
		$crtdata=[];	
		foreach ($certificates as $key => $value) {
			if(empty($value)){
				continue;
			}else{
				$JobCertificate = new JobCertificate();
				$JobCertificate->certificate = trim($value);
				$crtdata[]=$JobCertificate;
			}
		}
		if($jobrec->certificates()->saveMany($crtdata)){
			return [true, "Certificates saved"];	
		}else{
			return [false, "Certificates not saved"];	
		}
		
	}

	public static function addJobAddress($jobrec,$address){
		
		$JobAddress = new JobAddress();
		$JobAddress->job_id = $jobrec->id;
		$JobAddress->city_id = $address['city_id'];
		$JobAddress->street = trim($address['street']);
		$JobAddress->postal_code = $address['postal_code'];
		$JobAddress->longitude = $address['longitude'];
		$JobAddress->latitude = $address['latitude'];
		if($JobAddress->save()){
			return [true, "Address Saved"];
		} else {
			return [false, "Address Not saved"];
		}
	}

	public static function addSkills($jobrec, $skills, $isId=true){
		
		$newSkills = [];
		
		foreach ($skills as $key => $value) {
			list($tagSaved, $tagMessage, $new_tag) = GeneralRepo::findOrCreateTag($jobrec->job_title_id, $value, $isId);
			if($tagSaved && $new_tag) {
				$skill = new JobSkill();
				$skill->tag_id = $new_tag->id;
				$newSkills[] = $skill;
			}

			// $skill = new JobSkill();
			// $skill->tag_id = $value;
			// $newSkills[] = $skill;
		}
		if($jobrec->skills()->saveMany($newSkills)){
			return [true, "Skills saved"];
		}else{
			return [false, "Skills not saved"];
		}
		
	}

	public static function addkeyword($jobrec, $keywords){
		
		$newkeyword = [];
		foreach ($keywords as $key => $value) {
			if(empty($value)){
				continue;
			}else{
				$keyword = new JobKeyword();
				$keyword->keyword = trim($value);
				$newkeyword[] = $keyword;
			}
		}
		if($jobrec->Keyword()->saveMany($newkeyword)){
			return [true, "Keyword saved"];
		}else{
			return [false, "Keyword not saved"];
		}
		
	}

	public static function addweekly($jobrec, $weekly){
		
		$newWeekly = [];
		foreach ($weekly as $key => $value) {
			$weekly = new JobWeekday();
			$weekly->day = $value;
			$newWeekly[] = $weekly;
		}
		if($jobrec->jobWeekday()->saveMany($newWeekly)){
			return [true, "Weekday saved"];
		}else{
			return [false, "Weekday not saved"];
		}
		
	}

	public static function updateJob($id,$data,$renew_date) {
		
		if($data['salary_check']==1){
			$salary=$data["salary"];
		}else{
			$salary=0;
		}
		if(empty($data["ending_date"])){
			$end_date=NULL;
		}else{
			$end_date=\Carbon\Carbon::parse($data["ending_date"])->format('Y-m-d');
		}
		$job = Job::where('id', $id)->first();
		if($job){
			$job->employer_id = $data["employer_id"];
			$job->title = $data["title"];
			$job->vacancies = $data["vacancies"];
			$job->job_title_id = $data["job_title_id"];
			$job->education_id = $data["education_id"];
			$job->experience_id = $data["experience_id"];
			$job->job_type_id = $data["job_type_id"];
			$job->experience_level_id = $data["experience_level_id"];
			$job->starting_date = \Carbon\Carbon::parse($data["starting_date"])->format('Y-m-d');
			$job->ending_date = $end_date;
			$job->work_schedule_from = $data["work_schedule_from"];
			$job->work_schedule_to = $data["work_schedule_to"];
			$job->salary_type_id = $data["salary_type_id"];
			$job->salary = $salary;
			$job->pay_by_id = $data["pay_by_id"];
			$job->pay_period_id = $data["pay_period_id"];
			$job->benefits = $data["benefits"];
			$job->description = $data["description"];
			$job->expiration_date = \Carbon\Carbon::parse($data["expiration_date"])->format('Y-m-d');
			$job->renew_date=$renew_date;
			//$job->salary_range_id =$data["salary_range_id"];

			//$metaArray = ["days"=>isset($data["dates"]) ? $data["dates"] : []];
			$dateArray = isset($data["dates"]) ? $data["dates"] : [];
			$jobMeta = $job->jMeta();
			if($job->jobType && $job->jobType->day_selection == 1) {
				$jobMeta["days"] = $dateArray;
			} else {
				if(isset($jobMeta["days"])) {
					unset($jobMeta["days"]);
				}
			}
			$job->meta = json_encode($jobMeta);
			
			if($job->update()){
				return [true, "Update", $job];
			} else {
				return [false, "Not Update", null];
			}
		}else{
			return [false, "Not Update", null];
		}
	}

	public static function updateJobCertificates($jobrec, $certificates){

		$jobrec->certificates()->delete();
		$crtdata=[];	
		foreach ($certificates as $key => $value) {
			if(empty($value)){
				continue;
			}else{
				$JobCertificate = new JobCertificate();
				$JobCertificate->certificate = trim($value);
				$crtdata[]=$JobCertificate;
			}
		}
		$jobrec->certificates()->saveMany($crtdata);
		//exit('sdfsd');
		return [true, "Certificates update"];
	}

	public static function updateJobAddress($jobrec,$address){
		$JobAddress = JobAddress::where('job_id', $jobrec->id)->first();
		if($JobAddress){
			$JobAddress->city_id = $address['city_id'];
			$JobAddress->street = trim($address['street']);
			$JobAddress->postal_code = $address['postal_code'];
			$JobAddress->longitude = $address['longitude'];
			$JobAddress->latitude = $address['latitude'];
			if($JobAddress->update()){
				return [true, "Address update"];
			} else {
				return [false, "Not update Address"];
			}
		}else{
			return [false, "Not update Address"];
		}
	}

	public static function updateSkills($jobrec, $skills){
		$jobrec->skills()->delete();
		$newSkills = [];
		foreach ($skills as $key => $value) {
			list($tagSaved, $tagMessage, $new_tag) = GeneralRepo::findOrCreateTag($jobrec->job_title_id, $value, true);
			if($tagSaved && $new_tag) {
				$skill = new JobSkill();
				$skill->tag_id = $new_tag->id;
				$newSkills[] = $skill;
			}
			// $skill = new JobSkill();
			// $skill->tag_id = $value;
			// $newSkills[] = $skill;
		}
		$jobrec->skills()->saveMany($newSkills);
		return [true, "Skills update"];
	}

	public static function updatekeyword($jobrec, $keywords){
		$jobrec->Keyword()->delete();
		$newkeyword = [];
		foreach ($keywords as $key => $value) {
			if(empty($value)){
				continue;
			}else{
				$keyword = new JobKeyword();
				$keyword->keyword = trim($value);
				$newkeyword[] = $keyword;
			}
		}
		if($jobrec->Keyword()->saveMany($newkeyword)){
			return [true, "Keyword update"];
		}else{
			return [false, "Keyword Not update"];
		}
		
	}

	public static function updateweekly($jobrec, $weekly){
		$jobrec->jobWeekday()->delete();
		$newWeekly = [];
		foreach ($weekly as $key => $value) {
			$weekly = new JobWeekday();
			$weekly->day = $value;
			$newWeekly[] = $weekly;
		}
		$jobrec->jobWeekday()->saveMany($newWeekly);
		return [true, "Weekday update"];
	}

	public static function updateJobaction($id,$action){
		$job = Job::where('id', $id)->first();
		if($job){
			$job->status = $action;
			if($job->update()){
				return [true, "Update status", $job];
			} else {
				return [false, "Not Update status", null];
			}
		}else{
			return [false, "Not Update status", null];
		}
	}

	public static function countApplicationStatus(){
		$currentDate = \Carbon\Carbon::now()->format("Y-m-d");
    	$q = JobApplication::select(
	            "job_applications.*",
	            DB::raw('users.name'),
	            DB::raw('users.mobile_number'),
	            DB::raw('users.email_address'),
	            DB::raw('jobs.title'),
	            DB::raw('datediff(job_applications.created_at, "'.$currentDate.'") as days'),
	            DB::raw('count(job_applications.id) as countid'),
	            DB::raw("sum(if(job_applications.status='accepted',1,0)) as accepted_applications"),
	            DB::raw("sum(if(job_applications.status='rejected',1,0)) as rejected_applications"),
	            DB::raw("sum(if(job_applications.status='in-process',1,0)) as waiting_applications"),
	            DB::raw("sum(if(job_applications.is_guest='1',1,0)) as is_guest")
	        );
        
        $q->join('users', 'users.id', '=', 'job_applications.user_id');
        $q->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        return $Applications = $q->get();
	}

	public static function CountEmployerJObseekerAndAdmin(){
		$q = User::select(
	            "*",
	            DB::raw('count(id) as countid'),
	            DB::raw("sum(if(type='JOB_SEEKER',1,0)) as job_seeker"),
	            DB::raw("sum(if(level='BACKEND_ADMIN',1,0)) as backend_admin"),
	            DB::raw("sum(if(type='EMPLOYER',1,0)) as employer")
	        );
        return $user = $q->get();
	}

	public static function countCandidateStatus(){
		$currentDate = \Carbon\Carbon::now()->format("Y-m-d");
    	$q = JobApplication::select(
	            "job_applications.*",
	            DB::raw('users.name'),
	            DB::raw('users.mobile_number'),
	            DB::raw('users.email_address'),
	            DB::raw('jobs.title'),
	            DB::raw('datediff(job_applications.created_at, "'.$currentDate.'") as days'),
	            DB::raw('count(job_applications.id) as countid'),
	            DB::raw("sum(if(job_applications.is_guest='1',1,0)) as is_guest")
	        );
        
        $q->join('users', 'users.id', '=', 'job_applications.user_id');
        $q->join('jobs', 'jobs.id', '=', 'job_applications.job_id');
        $q->where('job_applications.status','accepted');
        return $candidates = $q->paginate(env('DEFAULT_ROWSIZE_PERPAGE'));
	}

	public static function addEmployerUser($data){

		$User = new User();
		$User->name = $data['name'];
		$User->mobile_number = $data['mobile_number'];
		$User->email_address = $data['email_address'];
		$User->password = bcrypt($data['password']);
		$User->type = User::TYPE_EMPLOYER;
		$User->level = User::LEVEL_FRONTEND_USER;
		$User->status = User::STATUS_ACTIVATED;

		if($User->save()){
			return [true, "User Added",$User];
		} else {
			return [false, "User Not Added",$User];
		}
	}

	public static function updateEmployerUser($userId,$data){
		$User = User::where('id', $userId)->first();
		if($User){
			$User->name = $data['name'];
			$User->mobile_number = $data['mobile_number'];
			$User->email_address = $data['email_address'];
			if($User->update()){
				return [true, "Update Added",$User];
			} else {
				return [false, "User Not Update",$User];
			}
		}
	}

	public static function addEmployer($user,$data){
		$Employer = new Employer();
		$Employer->user_id = $user->id;
		$Employer->recruiter_type_id = $data['recruiter_type_id'];
		$Employer->company_name = $data['company_name'];
		$Employer->description = $data['description'];
		
		if($Employer->save()){
			return [true, "Employer Added",$Employer];
		} else {
			return [false, "Employer Not Added",$Employer];
		}	
	}

	public static function updateEmployer($Employer,$data){
		
		$Employer->recruiter_type_id = $data['recruiter_type_id'];
		$Employer->company_name = $data['company_name'];
		$Employer->description = $data['description'];
		
		if($Employer->update()){
			return [true, "Employer saved",$Employer];
		} else {
			return [false, "Employer Not saved",$Employer];
		}	
	}

	public static function updateEmployerAction($id,$action){
		$User = User::where('id', $id)->first();
		if($User){
			$User->status = $action;
			if($User->update()){
				return [true, "Update status", $User];
			} else {
				return [false, "Not Update status", null];
			}
		}else{
			return [false, "Not Update status", null];
		}
	}

	public static function changePassword($data){

		$user=MyAuth::user("admin");
		if($user){
			$repo = new UserRepo();
			return $repo->updatePassword($user->id,$data['currentPassword'],$data['password']);
		}
		return [false, 'Invalid request, please try again'];
	}

	public static function jobDateValidation($startDate,$endDate, $todayValidation = true){
		if($endDate){
			if(($startDate = Utility::parseCarbonDate($startDate,"Y-m-d")) && ($endDate = Utility::parseCarbonDate($endDate,"Y-m-d"))) {
				$days=Utility::diffInDates($startDate->format("Y-m-d"),$endDate->format("Y-m-d"));
				if($days>=0){
					if($todayValidation) {
						if(Utility::date_greaterThanToday($startDate,"Y-m-d",true)){
							return [true, "true",null];		
						}else{
							return [false, "please enter start date greater then or equal to today date",null];		
						}
					} else {
						return [true, "true",null];		
					}
				}else{
					return [false, "please enter end date greater then or equal to start date",null];	
				}
			}else{
				return [false, "please enter start date and end date Y-m-d format" , null];	
			}
		}else{
			$startDate = Utility::parseCarbonDate($startDate,"Y-m-d");
			if($startDate){
				if($todayValidation) {
					if(Utility::date_greaterThanToday($startDate,"Y-m-d",true)){
						return [true, "true",null];		
					}else{
						return [false, "please enter start date greater then or equal to today date",null];		
					}
				} else {
					return [true, "true",null];		
				}
				// if(Utility::date_greaterThanToday($startDate,"Y-m-d",true)){
				// 	return [true, "true",null];		
				// }else{
				// 	return [false, "please enter start date greater then or equal to today date",null];		
				// }	
			}else{
				return [false, "please enter start date Y-m-d format",null];
			}
		}
	}

	public static function jobExpireDateValidation($expireDate){
		$expireDate = Utility::parseCarbonDate($expireDate,"Y-m-d");
		if($expireDate){
			if(Utility::date_greaterThanToday($expireDate,"Y-m-d",true)){
					return [true, "true",null];		
				}else{
					return [false, "please enter expire date greater then or equal to today date",null];		
				}
		}else{
			return [false, "please expire date Y-m-d format",null];
		}
	}

	public static function ApplicationStatusUpdate($id,$status){
		$JobApplication = JobApplication::where('id', $id)->first();
		if($JobApplication){
			$JobApplication->status = $status;
			if($JobApplication->update()){
				return [true, "Update"];
			} else {
				return [false, "User Not Update"];
			}
		}
	}

	public static function saveCmsPageByPageName($pageName, $data)
	{
		$CmsPage = CmsPage::where('page_name', $pageName)->first();
		if($CmsPage)
		{
			$CmsPage->page_title 	= $data['page_title'];
			$CmsPage->page_name 	= $data['page_name'];
			$CmsPage->page_content 	= $data['page_content'];
			if($CmsPage->update())
				return true;
		}
		else
		{
			$CmsPage = new CmsPage();
			$CmsPage->page_title 	= $data['page_title'];
			$CmsPage->page_name 	= $data['page_name'];
			$CmsPage->page_content 	= $data['page_content'];

			if($CmsPage->save())
				return true;
			else
				return false;
		}

	}
}