<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTitle extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function category() {
    	return $this->belongsTo('\App\Models\JobCategory', 'job_category_id', 'id');
    }

    public function tags() {
    	return $this->hasMany('\App\Models\Tag');
    }

    public function getTitle() {
    	return ucwords($this->title);
    }

    public function getCategoryTitle() {
        if($this->category) {
            return $this->category->getName();
        }
        return "";
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function($jobtitle) {
            $jobtitle->tags()->delete();
        });
    }
}
