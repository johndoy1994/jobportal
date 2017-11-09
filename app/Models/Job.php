<?php

namespace App\Models;

use App\Models\Employer;
use App\Models\JobApplication;
use App\Models\JobType;
use App\MyAuth;
use App\Repos\API\PublicRepo;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Job extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at','expiration_date','starting_date','ending_date','renew_date'];
    
    /////////////////////
    /// Scopes //////////
    /////////////////////

    public function scopeActiveOnly($q) {
        $q->where('jobs.status','=','active');
    }

    public function scopeNotEnded($q) {
        $q->where(function($q) {
            $q->whereNull('jobs.ending_date');
            $q->orWhere(function($q) {
                $q->whereNotNull('jobs.ending_date');
                $q->whereDate('jobs.ending_date','>=', \Carbon\Carbon::now()->format("Y-m-d"));
            });
        });
    }

    public function scopeNotExpired($subResults) {
        $currentDate = \Carbon\Carbon::now()->format("Y-m-d");
        $subResults->where(function($query) use($currentDate) {
            // where ( expiration_date is null or (expiration_date is not null && expiration_date <= DATE) )
            $query->whereNull('jobs.expiration_date');
            $query->orWhere(function($query) use($currentDate) {
                $query->whereNotNull('jobs.expiration_date');
                $query->whereDate('jobs.expiration_date', '>=', $currentDate);
            });
        });
    }

    /////////////////////
    /// Field Match /////
    /////////////////////

    public function fieldMatch($field, $data=null, $user = null) {
        if($user) {

        } else {
            $user = MyAuth::user();
        }
        return PublicRepo::jobFieldMatch($field, $this, $user, $data);
    }

    //////////////////////
    /// General Helper ///
    //////////////////////

    public function getTagIds() {
        $tags = [];
        foreach ($this->skills as $jobSkill) {
            $tags[] = $jobSkill->tag_id;
        }
        return $tags;
    }

    public function getWeekDays() {
        $days = [];
        foreach($this->jobWeekday as $jobWeekday) {
            $days[] = $jobWeekday->day;
        }
        return $days;
    }

    public function getCertificateLine() {
        $line = "";
        foreach($this->certificates as $certificate) {
            $line .= $certificate->certificate.",";
        }
        $line = trim($line);
        $line = trim($line, ",");
        return $line;
    }

    public function getPostalCode() {
        if($jobAddress = $this->jobAddresses) {
            return $jobAddress->getPostalCode();
        }
        return "";
    }

    public function getStreet() {
        if($jobAddress = $this->jobAddresses) {
            return $jobAddress->getStreet();
        }
        return "";
    }

    public function getCountryId() {
        if($jobAddress = $this->jobAddresses) {
            return $jobAddress->getCountryId();
        }
        return 0;
    }
    
    public function getStateId() {
        if($jobAddress = $this->jobAddresses) {
            return $jobAddress->getStateId();
        }
        return 0;
    }

    // public function getCityId() {
    //     if($jobAddress = $this->jobAddresses) {
    //         return $jobAddress->getCityId();
    //     }
    //     return 0;
    // }

    public function isValidPartDays($dates, $format = "Y-m-d") {
        return self::validPartDays($dates, $format, $this);
    }

    public static function validPartDays($dates, $format = "Y-m-d", $ignoreJobDays = null) {
        if(isset($ignoreJobDays)) {
            $meta = $ignoreJobDays->jMeta();
            if(isset($meta["days"])) {
                foreach($meta["days"] as $jobDay) {
                    $key = array_search($jobDay, $dates);
                    if($key !== false) {
                        unset($dates[$key]);
                    }
                }
            }
        }
        return Utility::validateDatesInArray($dates,"Y-m-d","date_greaterThanToday");
    }

    public function isRenewable(){
        $currentDate = \Carbon\Carbon::now();
        $days=Utility::diffInDates($this->renew_date->format("Y-m-d"),$currentDate->format("Y-m-d"));
        if($days <= 0){
          return [false,'you can not renew this job'];
        }else{
            return [true,'true'];
        }
    }

    public function isRepostable(){
        $currentDate = \Carbon\Carbon::now();
        $days=Utility::diffInDates($this->expiration_date->format("Y-m-d"),$currentDate->format("Y-m-d"));
        if($days<0){
          return [false,'you can not repost this job'];
        }else{
            return [true,'true'];
        }
    }

    public function setReaded($user = null, $guard="web") {
        if(!isset($user)) {
            if(MyAuth::check($guard)) {
                $user = MyAuth::user($guard);
            }
        }
        if(isset($user)) {
            PublicRepo::addReadedJob($this->id, $user->id);
            return true;
        }
        return false;
    }

    public function isReadedByUser($user=null, $guard="web") {
        if(!isset($user)) {
            if(MyAuth::check($guard)) {
                $user = MyAuth::user($guard);
            }
        }
        if(isset($user)) {
            return PublicRepo::getReadedJob($this->id, $user->id) ? true : false;
        }
        return false;
    }

    public function getMetaDays($sort=true) {
        $dates = isset($this->jMeta()["days"]) ? $this->jMeta()["days"] : [];
        if($sort) {
            Utility::sortDates($dates,"Y-m-d");
        }   
        return $dates;
    }

    public function hasDateInDays($date) {
        $dates = $this->getMetaDays(false);
        return in_array($date, $dates);
    }

    public function areValidDays($days) {
        if($this->jobType && $this->jobType->day_selection == 1) {
            $meta = $this->jMeta();
            if(isset($meta["days"])) {
                foreach($days as $day) {
                    if($parsedDay = Utility::parseCarbonDate($day,"Y-m-d")) {
                        if(!Utility::date_greaterThanToday($parsedDay->format("Y-m-d"),"Y-m-d")) {
                            return false;
                        }
                        if(!in_array($day, $meta["days"])) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function jMeta() {
        if($this->meta) {
            return json_decode($this->meta, true);
        }
        return [];
    }

    public function dayCountJsondecode($data) {
        if($data) {
            return json_decode($data, true);
        }
        return [];
    }

    public function isAlertSet($keywords="",$miles=10,$user=null) {
        if(!$user) {
            if(MyAuth::check()) {
                $user = MyAuth::user();
            }
        }

        if($user) {
            return PublicRepo::findJobAlert($user, [
                'job_categories_id' => $this->getCategoryId(),
                'job_title_id' => $this->getSubCategoryId(),
                'keywords' => $keywords,
                'radius' => $miles,
                'city_id' => $this->getCityId()
            ]);
        }

        return null;
    }

    public function isApplied($user = null) {
        if(!$user) {
            $user = MyAuth::check() ? MyAuth::user() : null;
        }

        if($user) {
            return PublicRepo::findJobApplication($user, $this);
        }

        return null;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getKeywords() {
        if($this->keywords) {
            return $this->keywords;
        }
        return  "";
    }

    public function getSubCategoryId() {
        if($this->jobTitle) {
            return $this->jobTitle->id;
        }
        return 0;
    }

    public function getCategoryId() {
        if($this->jobTitle) {
            return $this->jobTitle->job_category_id;
        }
        return 0;
    }

    public function getTitle() {
    	return ucwords($this->title);
    }

    public function getFullAddress() {

        if(!isset($this->full_address)) {
            if($this->jobAddresses) {
                return $this->jobAddresses->getFullAddress();
            }
        }

        return $this->full_address;
    }

    public function getAddressField($field) {
        if($this->jobAddresses) {
            return $this->jobAddresses->{$field};
        }
        return "";
    }

    public function getSalaryString() {
        $salary = $this->getSalary();

        if($salary == 0) {
            return "Negotiable";
        }

        if($this->salaryType) {
            return $this->salaryType->getTypeName().": $".$salary;
        }

        return $salary;
    }

    public function getSalaryTypeId() {
        if($this->salaryType) {
            return $this->salaryType->id;
        }
        return 0;
    }

    public function getSalary() {
    	return $this->salary;
    }

    public function getJobType() {
    	return $this->jobType;
    }

    public function getEmployer() {
    	return $this->employer;
    }

    public function getEmployerName() {
        if($this->employer) {
            return $this->employer->getFullname();
        }
        return "";
    }

    public function getEmployerEmailAddress() {
        if($this->employer) {
            return $this->employer->getEmailAddress();
        }
        return "";
    }

    public function getCompanyName() {
        if($this->employer) {
            return $this->employer->getCompanyName();
        }
        return "";
    }

    public function getJobTypeName() {
        if($this->jobType) {
            return $this->jobType->getName();
        }
        return "";
    }

    public function getExcerptDescription($words = 30) {
    	return Str::words($this->description, $words);
    }

    public function getCreatedAt($format="d-m-Y") {
        return \Carbon\Carbon::parse($this->created_at)->format($format);
    }

    public function getrenew_date($format="d-m-Y") {
        return \Carbon\Carbon::parse($this->renew_date)->format($format);
    }

    public function getDistanceString() {
        if(isset($this->distance)) {
            $distance = round($this->distance, 2);
            return "$distance miles";
        }
        return "";
    }

    public function getPostedDayString() {

        if(!isset($this->posted_days_ago)) {
            $this->posted_days_ago = Utility::diffInDates($this->renew_date->format('Y-m-d'), \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if(isset($this->posted_days_ago)) {
        	if($this->posted_days_ago == 1) {
        		return $this->posted_days_ago." day ago";
            } else if($this->posted_days_ago == 0) {
                return "Today";
        	} else {
        		return $this->posted_days_ago." day(s) ago";
        	}
        }
    }

    public function salaryType() {
        return $this->hasOne('\App\Models\SalaryType', 'id', 'salary_type_id');
    }

    public function SalaryRange() {
        return $this->hasOne('\App\Models\SalaryRange', 'id', 'salary_range_id');
    }

    public function isExpiringIn($days = 5, $addSuffix = true) {

        if($this->expiration_date) {
            $remainingDays = Utility::diffInDates(\Carbon\Carbon::now()->format('Y-m-d'),$this->expiration_date->format('Y-m-d'));
            if($remainingDays < $days) {
                if($remainingDays < 1) {
                    return "today";
                }
                return $remainingDays . ($addSuffix ? " days" : "");
            }
        }

        return false;
    }

    public function isExpired() {
        if($this->expiration_date) {
            $todayDate = \Carbon\Carbon::now();
            $expiringDate = $this->expiration_date;

            $remainingDays = $expiringDate->diffInDays($todayDate);
            return $remainingDays < 0;
        }
        return false;
    }

    public function isExpiredJob() {
        $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
        if($this->expiration_date) {
            $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
            $startDate = Utility::parseCarbonDate($todayDate,"Y-m-d");
            $days=Utility::diffInDates($startDate->format("Y-m-d"),$this->expiration_date->format("Y-m-d"));
            if($days >=0){
                return true;
            }else{
                return false;
            }
        }else{
             return true;   
        }
    }

    public function isEnded() {
        if($this->ending_date) {
            /// Old Logic
            // $todayDate = \Carbon\Carbon::now();
            // $endingDate = $this->ending_date;

            // $remainingDays = $endingDate->diffInDays($todayDate);
            // return $remainingDays < 0;

            $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
            return Utility::diffInDates($todayDate, $this->ending_date->format('Y-m-d')) < 0;
        }
        return false;
    }

    public function isEndedJob() {
        if($this->ending_date) {
             $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
             $startDate = Utility::parseCarbonDate($todayDate,"Y-m-d");
            $days=Utility::diffInDates($startDate->format("Y-m-d"),$this->ending_date->format("Y-m-d"));
            if($days >=0){
                return true;
            }else{
                return false;
            }
        }else{
            return true;    
        }
    }

    public function getEducationName() {
        if($this->education) {
            return $this->education->getName();
        }
        return "";
    }

    public function getExperienceString()
    {
        if($this->experience) {
            return $this->experience->getName();
        }
        return "";
    }

    public function getExperienceLevelString()
    {
        if($this->experience_level) {
            return $this->experience_level->getName();
        }
        return  "";
    }

    public function getCityName() {
        if($this->addresses && $this->addresses->city) {
            return $this->addresses->city->getName();
        }
        return "";
    }

    public function getJobCoordinates() {
        if($this->addresses) {
            return [$this->addresses->latitude, $this->addresses->longitude];
        }
        return [0,0];
    }

    public function getCityId() {
        if($this->addresses()->first() && $this->addresses()->first()->city) {
            return $this->addresses()->first()->city->id;
        }
        return 0;
    }

    //////////////////////////////
    /// Relationships ////////////
    //////////////////////////////

    public function experience_level() {
        return $this->hasOne('\App\Models\ExperienceLevel','id','experience_level_id');
    }

    public function experience() {
        return $this->hasOne('\App\Models\Experience','id','experience_id');
    }

    public function education() {
        return $this->hasOne('\App\Models\Education','id','education_id');
    }

    public function addresses() {
        return $this->hasOne('\App\Models\JobAddress', 'job_id', 'id');
    }

    public function employer() {
        return $this->hasOne('\App\Models\Employer', 'id', 'employer_id');
    }

    public function certificates() {
        return $this->hasMany('\App\Models\JobCertificate');
    }

    public function skills() {
        return $this->hasMany('\App\Models\JobSkill');
    }

    public function jobWeekday() {
        return $this->hasMany('\App\Models\JobWeekday');
    }

    public function Keyword() {
        return $this->hasMany('\App\Models\JobKeyword');
    }

    public function jobType() {
        return $this->hasOne('\App\Models\JobType', 'id','job_type_id');
    }

    public function applications() {
        return $this->hasMany('\App\Models\JobApplication');
    }

    //////////////////////////////
    /// Sagar ////////////////////
    //////////////////////////////

    public function jobTitle() {
    	return $this->hasOne('\App\Models\JobTitle', 'id', 'job_title_id');
    }

    public function jobAddresses() {
    	return $this->hasOne('\App\Models\JobAddress', 'job_id', 'id');
    }

    public function jobApplication() {
    	return $this->hasOne('\App\Models\JobApplication', 'job_id', 'id');
    }

    public function jobPayBy() {
        return $this->hasOne('\App\Models\PayBy', 'job_id', 'id');
    }

    public function jobPayPeriod() {
        return $this->hasOne('\App\Models\PayPeriod', 'job_id', 'id');
    }

     protected static function boot() {
        parent::boot();
        static::deleting(function($job) {
            $job->addresses()->delete();
            $job->certificates()->delete();
            $job->skills()->delete();
            $job->Keyword()->delete();
            $job->jobWeekday()->delete();
            $job->applications()->delete();
        });

        static::updated(function($job) {
            // if($job->jobType) {
            //     if($job->jobType->day_selection == 0) {
            //         $meta = $job->jMeta();
            //         Log::info("event:job:updated::", ["meta"=>$meta]);
            //         if(isset($meta["days"])) {
            //             unset($meta["days"]);
            //             Log::info("event:job:updated::unsettedd days", ["meta"=>$meta]);
            //             $job->meta = json_encode($meta);
            //             Log::info("event:job:updated::unsettedd days", ["newmeta"=>$job->meta]);
            //             $job->update();
            //         }
            //     }
            // }
        });
    }

    public function application_count(){
        //->where('status','!=',"accepted")
       return JobApplication::where('job_id','=',$this->id)->where('status','=','in-process')->count();
    }

    public function getAllJobApplication(){
        return PublicRepo::getAllJobApplication($this->id);
    }

    public function jobcertificateArray($datajobcerti)
    {
        $jobCertiData=array();
        foreach ($datajobcerti as  $value) {
            
            $jobCertiData[]=strtolower($value->getCertificateString());
        }
        return $jobCertiData;
    }
    public function getAllSkillNamesAsArray($jobSkills)
    {
        $jobSkillsData = array();
        foreach ($jobSkills as  $skill) {
            $jobSkillsData[]=$skill->getTagTitle();
        }
        return $jobSkillsData;
    }
}
