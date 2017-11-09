<?php

namespace App\Repos;

use App\Models\City;
use App\Models\Country;
use App\Models\Degree;
use App\Models\Education;
use App\Models\Employer;
use App\Models\Experience;
use App\Models\ExperienceLevel;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobCertificate;
use App\Models\JobKeyword;
use App\Models\JobSkill;
use App\Models\JobTitle;
use App\Models\JobType;
use App\Models\JobWeekday;
use App\Models\PayBy;
use App\Models\PayPeriod;
use App\Models\RecruiterType;
use App\Models\SalaryRange;
use App\Models\SalaryType;
use App\Models\State;
use App\Models\Tag;
use App\Models\User;
use App\Repos\API\PublicRepo;
use App\Repos\GeneralRepo;
use App\Utility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportRepo extends Repo {

	//////////////////////////////
	/////validation expretion////
	/////////////////////////////

	public static function alpha_numeric_space($value){
		return preg_match('/^[A-Za-z0-9 ]+$/u', $value);
	}

	public static function alpha_numeric_other($value){
		return preg_match("/^[A-Za-z0-9'.,&/- ]+$/u", $value);
	}
	
	///////////////////////////////
	/// Country ///////////////////
	///////////////////////////////

	public static function addCountry($name) {
		$first = Country::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$country = new Country();
			$country->name = $name;
			return $country->save();
		}
		return false;
	}

	public static function getAllCountry($isDate=false,$startDate=null,$endDate=null){
		$q=Country::select('name');
				if($isDate){
					$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
					$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
				}
				$q->orderBy('name');
		return	$result=$q->get();
		

	}

	public static function getAllCountrylist(){
		return Country::select('id','name')->orderBy('name')->get();
	}


	///////////////////////////////
	/// State /////////////////////
	///////////////////////////////

	public static function addState($name,$country_id) {
		$first = State::where('name', $name)->where('country_id',$country_id)->first();
		
		if($first) {	// already added...
		} else { // not added new one 
			$state = new State();
			$state->name = $name;
			$state->country_id = $country_id;
			return $state->save();
		}
		return false;
	}

	public static function getAllState($country_id,$isDate=null,$startDate=null,$endDate=null){
		$q= State::select('name');
			$q->where('country_id',$country_id);
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
			$result=$q->get();
			return $result;
	}


	///////////////////////////////
	/// City //////////////////////
	///////////////////////////////

	public static function addCity($name,$state_id,$latitude=0,$longitude=0) {
		$first = City::where('name', $name)->where('state_id',$state_id)->first();
		
		if($first) {	// already added...
		} else { // not added new one 
			$city = new City();
			$city->name = $name;
			$city->state_id = $state_id;
			$city->latitude = $latitude;
        	$city->longitude = $longitude;
			return $city->save();
		}
		return false;
	}

	public static function getAllCity($state_id,$isDate=null,$startDate=null,$endDate=null){
		$q=City::select('name');
			$q->where('state_id',$state_id);
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
			$result=$q->get();
		return $result;
	}

	///////////////////////////////
	/// Job Category //////////////
	///////////////////////////////

	public static function addJobCategory($name) {
		$first = JobCategory::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$JobCategory = new JobCategory();
			$JobCategory->name = $name;
			return $JobCategory->save();
		}
		return false;
	}

	public static function getAllJobCategory($isDate=null,$startDate=null,$endDate=null){
		$q= JobCategory::select('name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
			$result=$q->get();
			return $result;
	}

	///////////////////////////////
	/// JOb Title /////////////////
	///////////////////////////////

	public static function getAllJobTitlelist(){
		return JobCategory::select('id','name')->orderBy('name')->get();
	}

	public static function addJobTitle($name,$job_category_id) {
		$first = JobTitle::where('title', $name)->where('job_category_id',$job_category_id)->first();
		
		if($first) {	// already added...
		} else { // not added new one 
			$JobTitle = new JobTitle();
			$JobTitle->title = $name;
			$JobTitle->job_category_id = $job_category_id;
			return $JobTitle->save();
		}
		return false;
	}

	public static function getAllJobTitle($job_category_id,$isDate=null,$startDate=null,$endDate=null){
		$q=JobTitle::select('title');
			$q->where('job_category_id',$job_category_id);
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('title');
		$result=$q->get();
			return $result;
	}

	///////////////////////////////
	/// Salary  Type //////////////
	///////////////////////////////

	public static function addSalaryType($name) {
		$first = SalaryType::where('salary_type_name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$SalaryType = new SalaryType();
			$SalaryType->salary_type_name = $name;
			$SalaryType->order = SalaryType::getLastOrder() + 1;
			return $SalaryType->save();
		}
		return false;
	}

	public static function getAllSalaryType($isDate=null,$startDate=null,$endDate=null){
		$q= SalaryType::select('salary_type_name');
		if($isDate){
			$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
			$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
		}
		$q->orderBy('salary_type_name');
		$result=$q->get();
		return $result;
	}

	///////////////////////////////
	/// Salary Range ///////////////
	///////////////////////////////

	public static function addSalaryRange($range_from,$range_to,$salary_type_id) {
		$first = SalaryRange::where('range_from', $range_from)->where('range_to', $range_to)->where('salary_type_id',$salary_type_id)->first();
		
		if($first) {	// already added...
		} else { // not added new one 
			$SalaryRange = new SalaryRange();
			$SalaryRange->range_from = $range_from;
			$SalaryRange->range_to = $range_to;
			$SalaryRange->salary_type_id = $salary_type_id;
			return $SalaryRange->save();
		}
		return false;
	}

	public static function getAllSlaryTypelist(){
		return SalaryType::select('id','salary_type_name')->orderBy('salary_type_name')->get();
	}

	public static function getAllSalaryRange($salary_type_id,$isDate=null,$startDate=null,$endDate=null){
		$q=SalaryRange::select('range_from','range_to');
		$q->where('salary_type_id',$salary_type_id);
		if($isDate){
			$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
			$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
		}
		$result=$q->get();
		return $result;
	}


	///////////////////////////////
	/// Experience ////////////////
	///////////////////////////////

	public static function addExperience($name) {
		$first = Experience::where('exp_name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$Experience = new Experience();
			$Experience->exp_name = $name;
			$Experience->order = Experience::getLastOrder() + 1;
			return $Experience->save();
		}
		return false;
	}

	public static function getAllExperience($isDate=null,$startDate=null,$endDate=null){
		$q= Experience::select('exp_name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('exp_name');
		return $q->get();
	}

	///////////////////////////////
	/// Experience Level //////////
	///////////////////////////////

	public static function addExperienceLevel($name) {
		$first = ExperienceLevel::where('level', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$ExperienceLevel = new ExperienceLevel();
			$ExperienceLevel->level = $name;
			$ExperienceLevel->order = ExperienceLevel::getLastOrder() + 1;
			return $ExperienceLevel->save();
		}
		return false;
	}

	public static function getAllExperienceLevel($isDate=null,$startDate=null,$endDate=null){
		$q =ExperienceLevel::select('level');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('level');
		return $q->get();
	}

	///////////////////////////////
	/// Job Type  /////////////////
	///////////////////////////////

	public static function addJobType($name,$day_selection=0) {
		$first = JobType::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$JobType = new JobType();
			$JobType->name = $name;
			$JobType->day_selection = $day_selection;
			$JobType->order = JobType::getLastOrder() + 1;
			return $JobType->save();
		}
		return false;
	}

	public static function getAllJobType($isDate=null,$startDate=null,$endDate=null){
		$q= JobType::select('name','day_selection');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		$result=$q->get();
		return $result; 
	}

	///////////////////////////////
	/// Industry  /////////////////
	///////////////////////////////

	public static function addIndustry($name) {
		$first = Industry::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$Industry = new Industry();
			$Industry->name = $name;
			return $Industry->save();
		}
		return false;
	}

	public static function getAllIndustry($isDate=null,$startDate=null,$endDate=null){
		$q= Industry::select('name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		return $q->get();

	}

	///////////////////////////////
	/// Industry  /////////////////
	///////////////////////////////

	public static function addDegree($name) {
		$first = Degree::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$Degree = new Degree();
			$Degree->name = $name;
			return $Degree->save();
		}
		return false;
	}

	public static function getAllDegree($isDate=null,$startDate=null,$endDate=null){
		$q= Degree::select('name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		return $q->get();
	}

	///////////////////////////////
	/// Education  /////////////////
	///////////////////////////////

	public static function addEducation($name) {
		$first = Education::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$Education = new Education();
			$Education->name = $name;
			$Education->order = Education::getLastOrder() + 1;
			return $Education->save();
		}
		return false;
	}

	public static function getAllEducation($isDate=null,$startDate=null,$endDate=null){
		$q= Education::select('name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		return $q->get();
	}

	///////////////////////////////
	/// Tag //////////////////////
	///////////////////////////////

	public static function addTag($name,$job_title_id) {
		$first = Tag::where('name', $name)->where('job_title_id',$job_title_id)->first();
		
		if($first) {	// already added...
		} else { // not added new one 
			$Tag = new Tag();
			$Tag->name = $name;
			$Tag->job_title_id = $job_title_id;
			return $Tag->save();
		}
		return false;
	}

	public static function getAllTag($job_title_id,$isDate=null,$startDate=null,$endDate=null){
		$q=Tag::select('name');
			$q->where('job_title_id',$job_title_id);
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		$result=$q->get();
		return $result;
	}

	public static function getAllJobCategoryList(){
		return JobCategory::select('id','name')->orderBy('name')->get();
	}

	///////////////////////////////
	/// Recruitertype /////////////
	///////////////////////////////

	public static function addRecruiterType($name) {
		$first = RecruiterType::where('name', $name)->first();
		if($first) {	// already added...
		} else { // not added new one 
			$RecruiterType = new RecruiterType();
			$RecruiterType->name = $name;
			return $RecruiterType->save();
		}
		return false;
	}

	public static function getAllRecruiterType($isDate=null,$startDate=null,$endDate=null){
		$q= RecruiterType::select('name');
			if($isDate){
				$q->where('created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
			$q->orderBy('name');
		$result=$q->get();
		return $result;

	}

	///////////////////////////////
	/// Employer //////////////////
	///////////////////////////////

	public static function getRecruiter($recruiterName)
	{
		$data = RecruiterType::where('name', $recruiterName)->first();
		if($data){
			return [true, "get recruiter data Successfully", $data];
		}else{
			return [false, "Recruiter not there", null];
		}
	}

	public static function getCountry($countryName)
	{
		$data = country::where('name', $countryName)->first();
		if($data){
			return [true, "get country data Successfully", $data];
		}else{
			return [false, "Country not there", null];
		}	
	}

	public static function getState($stateName,$country)
	{
		if($country){
			$data = State::where('name', $stateName)->where('country_id', $country->id)->first();
			if($data){
				return [true, "get state data Successfully", $data];
			}else{
				return [false, "state not there", null];
			}
		}else{
			return [false, "state not there", null];
		}	
	}

	public static function getCity($cityName,$state)
	{
		if($state){
			$data = City::where('name', $cityName)->where('state_id', $state->id)->first();
			if($data){
				return [true, "get city data Successfully", $data];
			}else{
				return [false, "city not there", null];
			}	
		}else{
			return [false, "city not there", null];
		}
	}

	public static function addEmployerUser($name,$phone,$email){
		$User = new User();
		$userName=Utility::alpha_numeric_space($name);
		$useremail=Utility::email_validation($email);
		$userPhone=is_numeric($phone)? $phone : "";
		if($useremail && $userName){
			$User->name = $name;
			$User->mobile_number = $phone;
			$User->email_address = $email;
			$User->type = User::TYPE_EMPLOYER;
			$User->level = User::LEVEL_FRONTEND_USER;
			$User->status = User::STATUS_ACTIVATED;

			if($User->save()){
				return [true, "User Added",$User];
			} else {
				return [false, "User Not Added",$User];
			}
		}else{
			if(!$useremail && !$userName){
				return [false, "Please enter valid name and email",null];
			}elseif (!$useremail) {
				return [false, "Please enter valid email",null];
			}else{
				return [false, "Please enter valid name",null];
			}
		}
	}

	public static function addEmployer($RecruiterId,$UserId,$companyName, $compDescription){
		$Employer = new Employer();
		$Employer->user_id = $UserId;
		$Employer->recruiter_type_id = $RecruiterId;
		$Employer->company_name = $companyName;
		$Employer->description = $compDescription;

		if($Employer->save()){
			return [true, "Employer Added",$Employer];
		} else {
			return [false, "Employer Not Added",$Employer];
		}	
	}

	public static function checkUserEmail($email){
		$email_valid=Utility::email_validation($email);
		if($email_valid){
			$data = User::where('email_address', $email)->first();
			if($data){
				return [false, "email address is already exits",$data];
			} else {
				return [true, "",null];
			}
		}else{
			return [false, "Please enter valid email",null];
		}	
	}

	public static function checkvalidationUserphone($phone){
		if($phone){
			if(is_numeric($phone)){
				return [true, "",null];
			}else{
				return [false, "Please enter valid phone number",null];
			}
		}else{
			return [true, "",null];
		}
	}

	public static function getEmployerAllRecord($isDate=null,$startDate=null,$endDate=null){
		$q = Employer::select(
            "employers.*",
            DB::raw('users.mobile_number'),
            DB::raw('users.name as userName'),
            DB::raw('users.email_address'),
            DB::raw('users.status'),
            DB::raw('count(jobs.id) as job_count'),
            DB::raw('cities.id as cityId'),
            DB::raw('cities.name as cityname'),
            DB::raw('states.name as statename'),
            DB::raw('countries.name as countryname'),
            DB::raw('user_addresses.street'),
            DB::raw('user_addresses.postal_code'),
            DB::raw('recruiter_types.name as recruiterName')
        );
        $q->join('users', 'users.id', '=', 'employers.user_id');
		
		$q->leftJoin('recruiter_types', function($join) {
            $join->on('recruiter_types.id', "=", "employers.recruiter_type_id");
            $join->whereNull('recruiter_types.deleted_at');
        });

        $q->leftJoin('user_addresses', function($join) {
            $join->on('user_addresses.user_id', "=", "employers.user_id");
            $join->whereNull('user_addresses.deleted_at');
        });
        $q->leftJoin('cities', function($join) {
            $join->on('cities.id', "=", "user_addresses.city_id");
            $join->whereNull("cities.deleted_at");
        });
        $q->leftJoin('states', function($join) {
            $join->on('states.id', "=", "cities.state_id");
            $join->whereNull('states.deleted_at');
        });
        $q->leftJoin('countries', function($join) {
            $join->on('countries.id', "=", "states.country_id");
            $join->whereNull('countries.deleted_at');
        });

        $q->leftJoin("jobs", function($join) {
            $join->on("jobs.employer_id", '=', 'employers.id');
            $join->whereNull("jobs.deleted_at");
        });

        if($isDate){
			$q->where('employers.created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
			$q->where('employers.created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
		}
       return $q->groupBy("employers.id")->get();
	}

	///////////////////////////////
	/// job ///////////////////////
	///////////////////////////////
	
	public static function getJobCategory($JobCategoryName){
		$data = JobCategory::where('name', $JobCategoryName)->first();
		if($data){
			return [true, "job category record",$data];
		}else{
			$checkName=Utility::alpha_numeric_space($JobCategoryName);
			if($checkName){
				$JobCategory = new JobCategory();
				$JobCategory->name = $JobCategoryName;
				if($JobCategory->save()){
					return [true, "job category record",$JobCategory];
				}else{
					return [false, "please enter correct job category",null];
				}
			}else{
				return [false, "Please enter valid JobCategory name",null];
			}

		}
	}

	public static function getJobTitle($JobCategory,$jobTitle){
		if($JobCategory){
			$data = JobTitle::where('title', $jobTitle)->where('job_category_id', $JobCategory->id)->first();
			if($data){
				return [true, "job title record",$data];
			}else{
				$checkName=Utility::alpha_numeric_space($jobTitle);
				if($checkName){
					$JobTitle = new JobTitle();
					$JobTitle->title = $jobTitle;
					$JobTitle->job_category_id = $JobCategory->id;
					if($JobTitle->save()){
						return [true, "job title record",$JobTitle];
					}else{
						return [false, "please enter correct job title",null];
					}
				}else{
					return [false, "Please enter valid JobTitle name",null];
				}

			}
		}else{
			return [false, "job title not found some thing want to wrong in job category",null];
		}
	}

	public static function getEmployer($email){
		$user = user::where('email_address', $email)->first();
		if($user){
			$data = Employer::where('user_id', $user->id)->first();
			if($data){
				return [true, "employer record",$data];	
			}else{
				return [false, "employer not found in this email",null];	
			}
		}else{
			return [false, "employer email is not exits",null];
		}
	}

	public static function NumberofVacanciesvalidation($number){
		if(is_numeric($number)){
			return [true, "number is valid",$number];	
		}else{
			return [false, "Number of vacancies enter only numeric",null];	
		}
	}

	public static function getJobTag($jobTitle,$jobTags){
		$jobTags = ($jobTags) ? $jobTags : [];
		$newSkills = [];
			foreach ($jobTags as $key => $value) {
				list($tagSaved, $tagMessage, $new_tag) = GeneralRepo::findOrCreateTag($jobTitle->id, $value, true);
				if($tagSaved && $new_tag) {
					$skill = new JobSkill();
					$skill->tag_id = $new_tag->id;
					$newSkills[] = $skill;
				}
				$jobTitle->skills()->saveMany($newSkills);
				return [true, "Skills saved",null];
			}
	}

	public static function getEducation($name){
		$data = Education::where('name', $name)->first();
		if($data){
			return [true, "education record",$data];
		}else{
			return [false, "Please enter correct education name",null];
		}
	}

	public static function getExperience($name){
		$data = Experience::where('exp_name', $name)->first();
		if($data){
			return [true, "Experience record",$data];
		}else{
			return [false, "Please enter correct Experience name",null];
		}
	}

	public static function getExperienceLevel($experiencelevelname){
		$data = ExperienceLevel::where('level', $experiencelevelname)->first();
		if($data){
			return [true, "Experiencelevel record",$data];
		}else{
			return [false, "Please enter correct Experience Level name",null];
		}
	}

	public static function getJobType($name){
		$data = JobType::where('name', $name)->first();
		if($data){
			return [true, "JobType record",$data];
		}else{
			return [false, "Please enter correct JobType name",null];
		}
	}

	public static function CheckStartDateEndDateValidation($startDate,$endDate){
		if($endDate){
			if(($startDate = Utility::parseCarbonDate($startDate,"Y-m-d")) && ($endDate = Utility::parseCarbonDate($endDate,"Y-m-d"))) {
				$days=Utility::diffInDates($startDate->format("Y-m-d"),$endDate->format("Y-m-d"));
				if($days>=0){
					if(Utility::date_greaterThanToday($startDate,"Y-m-d",true)){
						return [true, "true",$days];		
					}else{
						return [false, "please enter start date greater then or equal to today date",null];		
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
				if(Utility::date_greaterThanToday($startDate,"Y-m-d",true)){
					return [true, "true",null];		
				}else{
					return [false, "please enter start date greater then or equal to today date",null];		
				}	
			}else{
				return [false, "please enter start date Y-m-d format",null];
			}
		}
	}

	public static function CheckExpireDateValidation($expireDate){
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

	public static function CheckStartTimeEndTimeValidation($from,$to){
		if(($startTime = Utility::parseCarbonDate($from, "H:i:s")) && ($endTime = Utility::parseCarbonDate($to, "H:i:s"))) {
			if($startTime<=$endTime) {
				return [true, "true",$startTime,$endTime];	
			}else{
				return [false, "please set time H:i:s format and start time less then end time",null,null];
			}
		}
	}

	public static function getSalaryType($salaryType){
		$data = SalaryType::where('salary_type_name', $salaryType)->first();
		if($data){
			return [true, "salary type record",$data];	
		}else{
			return [false, "salary type not found",null];
		}
	}

	public static function getsalaryRange($salaryType,$SalaryRange){
		if($salaryType){
				$SalaryRange = explode("-", $SalaryRange);
				
				if(isset($SalaryRange[0],$SalaryRange[1]) && is_numeric($SalaryRange[0]) && is_numeric($SalaryRange[1])){
					if($SalaryRange[0]<=$SalaryRange[1]){
						$data = SalaryRange::where('range_from', '<=',$SalaryRange[0])->where('range_to', '>=',$SalaryRange[1])->where('salary_type_id', '=', $salaryType->id)->first();	
						if($data){
							return [true, "salary range record",$data];
						}else{
							$Salary_range = new SalaryRange();
							$Salary_range->range_from = $SalaryRange[0];
							$Salary_range->range_to = $SalaryRange[1];
							$Salary_range->salary_type_id = $salaryType->id;
							if($Salary_range->save()){
								return [true, "salary range record",$Salary_range];
							}else{
								return [false, "please enter correct salary range",null];
							}
						}
					}else{
						return [false, "Please enter salary range correct 'start range less then to  end range' Ex.100-150",null];		
					}
					

				}else{
					return [false, "Please enter salary range correct Ex.100-150",null];		
				}
		}else{
			return [false, "salary range not found some thing want to wrong in salaryType",null];
		}
	}

	public static function salaryValaidation($salary){
		if($salary){
			if(is_numeric($salary)){
				return [true, "salary  record",$salary];
			}else{
				return [false, "please enter correct salary or enter numeric",null];
			}
		}elseif($salary==0){
			return [true, "salary  record",$salary];
		}
		else{
			return [false, "please enter salary",null];
		}
	}

	public static function getPayPeriod($PayPeriodName){
		$data = PayPeriod::where('name', $PayPeriodName)->first();
		if($data){
			return [true, "payperiod  record",$data];
		}else{
			return [false, "PayPeriod no found please enter correct",null];
		}
	}

	public static function getPayBy($payByName){
	
		$data = PayBy::where('name',$payByName)->first();
		
		if($data){
			return [true, "PayBy record",$data];
		}else{
			return [false, "PayBy no found please enter correct",null];
		}
	}

	public static function addJobs($employer_id,$title,$vacancies,$job_title_id,$education_id,$experience_id,$job_type_id,$experience_level_id,$starting_date,$ending_date,$work_schedule_from,$work_schedule_to,$salary_type_id,$salary,$pay_by_id,$pay_period_id,$benefits,$description,$expiration_date,$dataDates=null){
		if(empty($ending_date)){
			$end_date=NULL;
		}else{
			$end_date=\Carbon\Carbon::parse($ending_date)->format('Y-m-d');;
		}
		$job = new Job();
		$job->employer_id = $employer_id;
		$job->title = $title;
		$job->vacancies = $vacancies;
		$job->job_title_id = $job_title_id;
		$job->education_id = $education_id;
		$job->experience_id = $experience_id;
		$job->job_type_id = $job_type_id;
		$job->experience_level_id = $experience_level_id;
		$job->starting_date = \Carbon\Carbon::parse($starting_date)->format('Y-m-d');
		$job->ending_date = $end_date;
		$job->work_schedule_from = $work_schedule_from;
		$job->work_schedule_to = $work_schedule_to;
		$job->salary_type_id = $salary_type_id;
		$job->salary = $salary;
		$job->pay_by_id = $pay_by_id;
		$job->pay_period_id = $pay_period_id;
		$job->benefits = $benefits;
		$job->description = $description;
		$job->expiration_date = \Carbon\Carbon::parse($expiration_date)->format('Y-m-d');
		$job->status ="active";
		$job->renew_date = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
		//$job->salary_range_id =$salary_range_id;
		$metaArray = ["days"=>isset($dataDates) ? $dataDates : []];
		$job->meta = json_encode($metaArray);
		if($job->save()){
			return [true, "Saved", $job];
		} else {
			return [false, "Not saved", null];
		}
	}

	public static function getAllJobs($isDate=null,$startDate=null,$endDate=null){

		$q = Job::select(
                "jobs.*",
                DB::raw('job_titles.title as jobTitle'),
                DB::raw('job_categories.name as category_name'),
                DB::raw('employers.company_name'),
                DB::raw('users.email_address'),
                DB::raw('education.name as educationName'),
                DB::raw('experiences.exp_name'),
                DB::raw('experience_levels.level'),
                DB::raw('job_types.name as jobTypeName'),
                DB::raw('job_addresses.postal_code'),
                DB::raw('job_addresses.street'),
                DB::raw('countries.name as countryname'),
                DB::raw('states.name as statename'),
                DB::raw('cities.name as cityname'),
                DB::raw('salary_types.salary_type_name'),
                // DB::raw('salary_ranges.range_from'),
                // DB::raw('salary_ranges.range_to'),
                DB::raw('pay_bies.name as PayByName'),
                DB::raw('pay_periods.name as PayperiodsName')
            );

        	$q->join('job_titles','job_titles.id', "=", "jobs.job_title_id");
        	$q->join('job_categories','job_categories.id', "=", "job_titles.job_category_id");	
        	$q->join('employers', 'employers.id', '=', 'jobs.employer_id');	
        	$q->join('users','users.id', "=", "employers.user_id");
        	$q->join('education','education.id', "=", "jobs.education_id");
        	$q->join('experiences','experiences.id', "=", "jobs.experience_id");
        	$q->join('experience_levels','experience_levels.id', "=", "jobs.experience_level_id");
        	$q->join('job_types','job_types.id', "=", "jobs.job_type_id");	
        	$q->join('job_addresses','job_addresses.job_id', "=", "jobs.id");
        	$q->join('cities','cities.id', "=", "job_addresses.city_id");
        	$q->join('states','states.id', "=", "cities.state_id");
        	$q->join('countries','countries.id', "=", "states.country_id");
        	$q->join('salary_types','salary_types.id', "=", "jobs.salary_type_id");
        	//$q->join('salary_ranges','salary_ranges.id', "=", "jobs.salary_range_id");
        	$q->join('pay_bies','pay_bies.id', "=", "jobs.pay_by_id");
        	$q->join('pay_periods','pay_periods.id', "=", "jobs.pay_period_id");
        	if($isDate){
				$q->where('jobs.created_at',">=",\Carbon\Carbon::parse($startDate)->format('Y-m-d 00:00:00'));
				$q->where('jobs.created_at',"<=",\Carbon\Carbon::parse($endDate)->format('Y-m-d 23:59:59'));
			}
        	return $q->get();
    }

    public static function getJobsSkill($jobId){
    	$q = JobSkill::select(
                "job_skills.*",
                DB::raw('tags.name as tagName')
            );

        	$q->join('tags','tags.id', "=", "job_skills.tag_id");
        	$q->where('job_skills.job_id', $jobId);
        	return $q->groupBy("job_skills.id")->get();
    }

    public static function getJobscertificate($jobId){
    	return JobCertificate::select('certificate')->where('job_id', $jobId)->orderBy('certificate')->get();
    }

    public static function getJobsKeyword($jobId){
    	return JobKeyword::select('keyword')->where('job_id', $jobId)->orderBy('keyword')->get();
    }

    public static function getJobsWeekdays($jobId){
    	return JobWeekday::select('day')->where('job_id', $jobId)->orderBy('day')->get();
    }

    ///////////////////////////////
	/// jobseeker /////////////////
	///////////////////////////////

    public static function checkName($name)
    {
    	$data= [false, "Please enter correct name",null];
    	if(Utility::alpha_numeric_space($name)){
    		$data= [true, "Successfully",null];
    	}
    	return $data;
    }

    public static function checkEmail($email)
    {
    	$data= [false, "Please enter correct email",null];
    	if(Utility::email_validation($email)){
    		$UserData=User::where('email_address','=',$email)->first();
    		if(!$UserData){
    			$data= [true, "Successfully",null];
    		}else{
    			$data= [false, "email address is already exits",null];
    		}
    		
    	}
    	return $data;
    }

    public static function checkMobile($mobile){
    	$data= [false, "Please enter correct name",null];
    	if($mobile!=0){
    		if(is_numeric($mobile)){
    			if(Utility::mobile_validation($mobile)){
    				$UserData=User::where('mobile_number','=',$mobile)->first();
    				if(!$UserData){
		    			$data= [true, "Successfully",null];
		    		}else{
		    			$data= [false, "mobile number is already exits",null];
		    		}
    			}	
    		}else{
    			$data= [false, "Please enter Mobile NUmber only numeric",null];
    		}
    				
    	}else{
    		$data= [true, "Successfully",null];
    	}
    	
    	return $data;	
    }

    public static function checkJobType($jobtype){
    	$jobtypeData = (isset($jobtype)) ? $jobtype : "";
        $jobtype = explode(",", $jobtypeData);
        $crtdata=[];
        foreach ($jobtype as $key => $value) {
        	$jobtyperec=JobType::where('name','=',$value)->first();
        	if($jobtyperec){
        		$crtdata[]=$jobtyperec->id;
        	}
        }
    	$data= [false, "Please check job type is missmatch or incorrect.!",null];
    	if(count($crtdata)>0){
    		$data= [true, "Successfully",$crtdata];
    	}
    	return $data;
    }

    public static function checkPrivacy($option){
    	$data= [false, "please enter option value 1 or 2 or 3",null];	
    	$privacy=['1','2','3'];
    	if(is_numeric($option) &&  in_array($option, $privacy)){
    		$data= [true, "Successfully",$option];	
    	}
    	return $data;
    }

    public static function checkPartTimeDays($dates,$jobType){
    	$DateArray=array();
    	if($jobType){
    		if($jobType->day_selection==1){
    			$partTimeDay = (isset($dates)) ? $dates : [];
    			$partTimeDayData = explode(",", $partTimeDay);
                
                foreach ($partTimeDayData as $key => $value) {
                	list($status[0],$message[0],$data)=self::CheckExpireDateValidation($value);
                	if($status[0]){
                		$DateArray[]=$value;
                	}
                }
                if(!empty($DateArray)){
                	$data= [true, "Successfully",$DateArray];	
                }else{
                	$data= [false, "Please enter part time days",null];	
                }
                
    		}else{
    			$data= [true, "Successfully",null];		
    		}
    	}else{
    		$data= [false, "please enter jobType",null];	
    	}
    	return $data;
    }

     public static function jMetaDecode($meta) {
        if($meta) {
            return json_decode($meta, true);
        }
        return [];
    }

}

