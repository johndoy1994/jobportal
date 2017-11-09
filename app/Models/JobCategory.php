<?php

namespace App\Models;

use App\Repos\API\PublicRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCategory extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function jobtitles(){
    	return $this->hasMany('\App\Models\JobTitle')->orderBy('title');
    }

    public function getName() {
    	return ucwords($this->name);
    }

    public function getJobCount() {
        return PublicRepo::jobCountOf($this->id,'job_categories');
    }

    protected static function boot() {
  		parent::boot();
  		static::deleting(function($category) {
  			$category->jobtitles()->delete();
  		});
  	}
}
