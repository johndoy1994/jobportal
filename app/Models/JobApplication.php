<?php

namespace App\Models;

use App\Models\Job;
use App\Models\UserProfile;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use SoftDeletes;
  
  	protected $dates = ['deleted_at'];

    public function getApplicationRef() {
      if($this->job && $this->job->employer && $this->user) {
        return $this->job->id.$this->job->employer->id.$this->user->id.$this->job_id;
      }
    }

    public function is_visible_cv($jobApplication_userId){
      $user_Data=UserProfile::where('user_id','=',$jobApplication_userId)->first();
      if($user_Data){
         if($user_Data->profile_privacy==1){
          return true;
         }else{
          return false;
         } 
      }else{
        return false;
      }
    }

    public function is_uploade_resume($jobApplication_userId){
      return $user_Data=UserResume::where('user_id','=',$jobApplication_userId)->first();
    }

    public function isInProcess() {
        return $this->status == "in-process";
    }

    public function isRejected() {
        return $this->status == "rejected";
    }

    public function isAccepted() {
        return $this->status == "accepted";
    }

    public function isCancelled() {
        return $this->status == "cancelled";
    }

    public function cancelApplication() {
        if($this->isInProcess() || $this->isRejected() || $this->isAccepted() ) {
            $this->status = "cancelled";
            return $this->update();
        }
        return false;
    }

  	public function appliedOnString() {
  		return $this->created_at->format('d-m-Y');
  	}

  	public function daysAgoAppliedString() {	
  		$days = $this->created_at->diffInDays(\Carbon\Carbon::now());

  		if($days <= 0) {
  			return "applied today";
  		} elseif($days == 1) {
  			return "applied yesterday";
  		} else {
  			return "applied $days days ago";
  		}
  	}

    public function getMatchingStatus() {
        return PublicRepo::getMatchCount($this->user, $this->job);
    }

    public function getJobTitle() {
        if($this->job) {
            return $this->job->getTitle();
        }
        return "";
    }

    public function getStatus() {
      switch($this->status) {
        case "in-process": return "In Process";
        case "accepted": return "Accepted";
        case "rejected": return "Rejected";
        case "cancelled": return "Cancelled";
        default: return "N/A";
      }
      return "";
    }

  	// relationships

  	public function job() {
  		return $this->hasOne('\App\Models\Job','id','job_id');
  	}

  	public function user() {
  		return $this->hasOne('\App\Models\User','id','user_id');
  	}

    public function isGuest() {
        if($this->is_guest) {
          return true;
        }
        return false;
    }

    public function saveApplicationDetails($jobapplication,$status){
      
      if($jobapplication) {
            $jobapplication->status = $status;
            return $jobapplication->update();
        }
        return false;
    }

    public static function getRef($user_id) {
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

}
