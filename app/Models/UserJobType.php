<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserJobType extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getTypeId() {
    	if($this->jobType) {
    		return $this->jobType->id;
    	}
    	return 0;
    }

    public function getTypeName() {
    	if($this->jobType) {
    		return $this->jobType->getName();
    	}
    	return "N/A";
    }

    public function jobType() {
    	return $this->hasOne('\App\Models\JobType', 'id', 'job_type_id');
    }
}
