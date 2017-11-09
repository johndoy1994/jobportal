<?php

namespace App\Models;

use App\Models\JobApplication;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract
{

	// constants
	const TYPE_SEEKER = "JOB_SEEKER";
	const TYPE_EMPLOYER = "EMPLOYER";
	const TYPE_BACKEND = "BACKEND";

	const LEVEL_FRONTEND_USER = "FRONTEND";
	const LEVEL_BACKEND_ADMIN = "BACKEND_ADMIN";
	const TYPE_SALES = "SALES";

	const STATUS_ACTIVATED = "ACTIVATED";
	const STATUS_DEACTIVATED = "DEACTIVATED";

	// model

	use SoftDeletes, Authenticatable;
	protected $dates = ['deleted_at'];

	protected $fillable = ['name','mobile_number','email_address','password','type','level','status','verified','through_id'];


	// relationships

	public function instant_match() {
		return $this->hasOne('\App\Models\InstantMatch','user_id','id');
	}

	public function job_types() {
		return $this->hasMany('\App\Models\UserJobType','user_id','id');
	}

	public function profile() {
		return $this->hasOne('\App\Models\UserProfile');
	}

	public function verification() {
		return $this->belongsTo('\App\Models\UserVerification','id','user_id');
	}

	public function addresses() {
		return $this->hasMany('\App\Models\UserAddress');
	}

	public function applications() {
		return $this->hasMany("\App\Models\JobApplication");
	}

	public function experiences() {
		return $this->hasMany('\App\Models\UserExperience');
	}

	public function certificates() {
		return $this->hasMany('\App\Models\UserCertificate');
	}

	public function skills() {
		return $this->hasMany('\App\Models\UserSkill');
	}

	public function resumes() {
		return $this->hasMany("\App\Models\UserResume");
	}

	public function saved_jobs() {
		return $this->hasMany("\App\Models\SavedJob");
	}

	public function job_alerts() {
		return $this->hasMany("\App\Models\JobAlert")->withTrashed();
	}

	public function employer() {
		return $this->hasOne('\App\Models\Employer','user_id','id');
	}

	// employer functions



	// functions

	public function showContactDetails() {
		if($this->profile) {
			return $this->profile->profile_privacy == UserProfile::PUBLIC_PROFILE;
		}
		return false;
	}

	public function getAboutMe() {
		if($this->profile) {
			return $this->profile->about_me;
		}
		return "";
	}

	public function getHighestEducationString() {
		if($exp = $this->experiences()->first()) {
			
			if($exp->education) {
				return $exp->education->getName();
			}
		}
		return "";
	}

	public function hasMobile() {
		return $this->mobile_number > 0;
	}

	public function hasEmailAddress() {
		return !empty($this->email_address) || strlen(trim($this->email_address)) > 0;
	}

	public function isJobSeeker() {
		return $this->type == User::TYPE_SEEKER && $this->level == User::LEVEL_FRONTEND_USER;
	}

	public function isEmployer() {
		return $this->type == User::TYPE_EMPLOYER && $this->level == User::LEVEL_FRONTEND_USER;
	}

	public function isAdministrator() {
		//return $this->type == User::TYPE_BACKEND && $this->level == User::LEVEL_BACKEND_ADMIN;
		return ($this->type == User::TYPE_BACKEND || $this->type == User::TYPE_SALES) && $this->level == User::LEVEL_BACKEND_ADMIN;
	}

	public function isActivated() {
		return $this->status == User::STATUS_ACTIVATED;
	}

	public function getName() {
		if(strlen(trim($this->name)) == 0) {
			return "No Name";
		}
		return ucwords($this->name);
	}

	public function getFirstName() {
		$name = $this->getName();
		$names = explode(" ", $name);

		$firstname = "";
		for($i=0;$i<count($names)-1;$i++) {
			$firstname .= $names[$i]." ";
		}

		if(strlen(trim($firstname)) == 0) {
			return $this->getName();
		}

		return trim($firstname);
	}

	public function getLastName() {
		$name = $this->getName();
		$names = explode(" ", $name);
		if(count($names) <= 1) {
			return "";
		}
		return isset($names[count($names)-1]) ? $names[count($names)-1] : "";
	}

	public function getEmailAddress() {
		return $this->email_address;
	}

	public function getMobileNumber() {
		if($this->mobile_number > 0) {
			return $this->mobile_number;
		}
		return "";
	}

	public function validateUser($type, $level) {
		
		if(strtolower($level) === strtolower($this->level)) {

			if($type === "*") {
				return true;
			}

			return strtolower($type) === strtolower($this->type);

		}

		return false;
	}

	public function getDesiredLocations() {
		$userAddress = $this->addresses()->where('type','desired')->get();
		$allAddresses = [];
		foreach($userAddress as $userAdd) {
			$allAddresses[] = $userAdd->getFullLine();
		}
		return $allAddresses;
	}

	public function getDesiredLocations_cities() {
		$userAddress = $this->addresses()->where('type','desired')->get();
		$allCities = [];
		foreach($userAddress as $userAdd) {
			$allCities[] = $userAdd->city;
		}
		return $allCities;
	}

	public function getResidanceAddress() {
		$userAddress = $this->addresses()->where('type','residance')->first();
		if($userAddress) {
			return $userAddress->getFullLine();
		}
		return "N/A";
	}

	public function getPersonTitle() {
		if($this->profile) {
			if($this->profile->title) {
				return $this->profile->title->getName();
			}
		}
		return "";
	}

	public function getJobTypeString() {
		$jobTypes = $this->job_types;
		$userJobTypes = [];
		foreach($jobTypes as $jobType) {
			$userJobTypes[] = $jobType->getTypeName();
		} 
		$finalString = implode(', ', $userJobTypes);
		return strlen($finalString) == 0 ? 'N/A' : $finalString;
	}

	// Only for job seeker methods

	public function getCertificatesLine() {
		$certificates = $this->certificates;
		$line = "";
		foreach($certificates as $cert) {
			$line .= "$cert->certificate, ";
		}
		return trim($line,",");
	}

	public function certificateArray($datacerti)
	{
		$userCertiData=array();
		foreach ($datacerti as  $value) {

			$userCertiData[]=strtolower($value->getName());
		}
		return $userCertiData;
	}
	public function getAllSkillNamesAsArray() {
		$userSkillsData = array();

		foreach($this->skills as $skill) {
			if($skill->tag) {
				$userSkillsData[] = $skill->tag->getName();
			}
		}
		return $userSkillsData;
	}
	
	public function getSkillsLine() {
		$line = "";
		foreach($this->skills as $skill) {
			if($skill->tag) {
				$line .= $skill->tag->getName().", ";
			}
		}
		return trim($line,",");
	}

	public function getEducationName() {
		if($exp = $this->experiences()->first()) {
			if($exp->education) {
				return $exp->education->getName();
			}
		}
		return "";
	}

	public function getRecentJobTitle() {
		if($exp = $this->experiences()->first()) {
			return $exp->getRecentJobTitle();
		}
		return "";
	}

	public function getExperienceName() {
		if($exp = $this->experiences()->first()) {
			if($exp->experience) {
				return $exp->experience->getName();
			}
		}
		return "";
	}

	public function getExperienceLevelName() {
		if($exp = $this->experiences()->first()) {
			if($exp->experience_level) {
				return $exp->experience_level->getName();
			}
		}
		return "";
	}

	public function getCurrentSalaryString() {
		if($exp = $this->experiences()->first()) {
			if($exp->current_salary_range) {
				if($exp->current_salary_range->salaryType) {
					return $exp->current_salary_range->salaryType->getName()." : ".$exp->current_salary_range->range();
				}
				return $exp->current_salary_range->range();
			}
		}
		return "";	
	}

	public function getDesiredJobTitleName() {
		if($exp = $this->experiences()->first()) {
			if($exp->desired_job_title) {
				return $exp->desired_job_title->getTitle();
			}
		}
		return "";	
	}

	public function getDesiredSalaryString() {
		if($exp = $this->experiences()->first()) {
			if($exp->desired_salary_range) {
				if($exp->desired_salary_range->salaryType) {
					return $exp->desired_salary_range->salaryType->getName()." : ".$exp->desired_salary_range->range();
				}
				return $exp->desired_salary_range->range();
			}
		}
		return "";	
	}

	public function getMatchingStatus($UserData,$Job) {
        return PublicRepo::getMatchCount($UserData,$Job);
    }

    public function getUserApplication($UserData,$Job){
    	return JobApplication::where('user_id',$UserData->id)->where('job_id',$Job->id)->first();
    }

    public static function UserChatgetRef($user_id) {
      $user = MyAuth::user('recruiter');
      $ids=[$user->id,$user_id];
    for($i=0;$i<count($ids);$i++) {
      for($j=0;$j<count($ids);$j++) {
        if($ids[$i] < $ids[$j]) {
          $temp = $ids[$i];
          $ids[$i] = $ids[$j];
          $ids[$j] = $temp;
        }
      }
    }
    return implode('', $ids);
  }

  public static function isUserValidForConversation($conversation_ref,$loginType){
  		$User=MyAuth::user($loginType);
    	$messages = Messages::where('conversation_ref', $conversation_ref)->where('is_message', "!=",1)->first();
    	if($messages && $User){
    		$user_id=$User->id;
    		return $messages->isReceiver($user_id) || $messages->isSender($user_id);	
    	}
    	return false;
    }
    public function dayCountJsondecode($data) {
        if($data) {
            return json_decode($data, true);
        }
        return [];
    }

    public function getConversationInfo() {
        
        $data=array();
        if($this->mobile_number) {
            $data[] =$this->mobile_number;
        }
        if($this->email_address) {
            $data[] =$this->email_address;
        }
        $info =' ('.implode(', ', $data).')';
        return $info;
    }    
	// events

	protected static function boot() {
		parent::boot();
		static::deleting(function($user) {
			$user->job_types()->delete();
			$user->profile()->delete();
			$user->verification()->delete();
			$user->addresses()->delete();
			$user->experiences()->delete();
			$user->certificates()->delete();
			$user->skills()->delete();
			$user->resumes()->delete();
		});
		static::saved(function($user) {
			
		});
	}

	Public static function is_resume($cv){
		if($cv){
			return UserResume::where('user_id','=',$cv->id)->first();	
		}else{
			return null;
		}
		
	}

}
