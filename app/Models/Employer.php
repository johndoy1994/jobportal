<?php

namespace App\Models;

use App\Models\JobApplication;
use App\Utility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employer extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getCompanyName() {
    	return ucwords($this->company_name);
    }

    public function getFullname() {
    	if($this->user) {
    		return $this->user->getName();
    	}
    	return "";
    }

    public function getEmailAddress() {
    	if($this->email_address && strlen($this->email_address) > 0) {
    		return $this->email_address;
    	} else {
    		if($this->user) {
    			return $this->user->email_address;
    		}
    	}

    	return "";
    }

    // Relationships

    public function recruiterType(){
        return $this->hasOne('\App\Models\RecruiterType','id','recruiter_type_id');
    }

    public function user() {
    	return $this->hasOne('\App\Models\User','id','user_id');
    }

    public function jobs() {
        return $this->hasMany('\App\Models\Job','employer_id','id');
    }

    

    protected static function boot() {
        parent::boot();
        static::deleting(function($employer) {
            $employer->user()->delete();
            $employer->jobs()->delete();
        });
    }

}