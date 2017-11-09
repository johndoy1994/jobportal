<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAlert extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function user() {
    	return $this->belongsTo("\App\Models\User");
    }

    public function city() {
    	return $this->hasOne("\App\Models\City", 'id', 'city_id');
    }

    public function salaryType() {
    	return $this->hasOne('\App\Models\SalaryType', 'id', 'salary_type_id');
    }

    public function jobCategory() {
        return $this->hasOne('\App\Models\JobCategory','id','job_categories_id');
    }

    public function jobTitle() {
        return $this->hasOne('\App\Models\JobTitle','id','job_title_id');
    }

    public function jobType() {
        return $this->hasOne('\App\Models\JobType','id','job_type_id');
    }

    public function industry() {
        return $this->hasOne('\App\Models\Industry','id','industries_id');
    }

    ////////////////////////////
    /// Helper Methods /////////
    ////////////////////////////

    public function getAlertTitle() {
        if($this->jobTitle) {
            return $this->jobTitle->getTitle();
        } else if($this->jobCategory) {
            return $this->jobCategory->getName();
        } else if(strlen($this->keywords)>0) {
            return $this->keywords;
        } else if($this->jobType) {
            return $this->jobType->getName();
        } else if($this->industry) {
            return $this->industry->getName();
        } else {
            return "Any";
        }
    }

    public function getSearchParams() {
    	$params = [];

    	// Keywords
    	$params["keywords"] = $this->keywords;

    	// Location
    	$params["location"] = "";
    	if($this->city) {
    		$params["location"] = $this->city->getName();
    	}

    	$params["radius"] = $this->radius;

    	$params["salaryType"] = $this->salary_type_id;

    	$params["salaryRate"] = $this->salary_range_from;

    	$params["jobType"] = $this->job_type_id;

        if($this->job_title_id > 0) {
            $params["jobTitle"] = $this->job_title_id;
        } else if($this->job_categories_id > 0) {
            $params["jobCategory"] = $this->job_categories_id;
        }

        $params["sortBy"] = "date";

    	return $params;
    }
}
