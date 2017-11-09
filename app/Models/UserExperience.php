<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExperience extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getRecentJobTitle() {
        return ucwords($this->recent_job_title);
    }

    public function education() {
    	return $this->hasOne('\App\Models\Education','id','education_id');
    }

    // removed as multiple job types are added in own table named UserJobType
    // public function current_job_type() {
    // 	return $this->hasOne('\App\Models\JobType', 'id', 'current_job_type_id');
    // }

    public function current_salary_range() {
    	return $this->hasOne('\App\Models\SalaryRange', 'id', 'current_salary_range_id');
    }

    public function experience() {
    	return $this->hasOne('\App\Models\Experience','id','experinece_id');
    }

    public function experience_level() {
    	return $this->hasOne('\App\Models\ExperienceLevel','id','experinece_level_id');
    }

    public function desired_job_title() {
    	return $this->hasOne('\App\Models\JobTitle', 'id', 'desired_job_title_id');
    }

    public function desired_salary_range() {
    	return $this->hasOne('\App\Models\SalaryRange', 'id', 'desired_salary_range_id');
    }

}
