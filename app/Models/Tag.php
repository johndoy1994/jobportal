<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    Use SoftDeletes;    

	protected $dates = ['deleted_at'];

	public function jobtitle() {
		return $this->belongsTo('\App\Models\JobTitle','job_title_id', 'id');
	}

	public function getjobtitleName()
  	{
		return ucwords($this->job_titles);
  	}
  	
	public function getName()
  	{
		return ucwords($this->name);
  	}
}
