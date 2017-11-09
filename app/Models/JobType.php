<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class JobType extends Model
{
    Use SoftDeletes;    

	protected $dates = ['deleted_at'];

  public static function isDaySelection($id) {
    if(JobType::where('id',$id)->where('day_selection',1)->first()) {
      return true;
    }  
    return false;
  }

	public function getName()
 	{
		return ucwords($this->name);
  	}

	public static function getFirstOrder($min=null) {

      $q = JobType::select("order");
      if($min) {
        $q->where('order','>',$min);
      }
      $q->orderBy('job_types.order');
      $lastItem = $q->first();
      
      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

  	public static function getLastOrder($max=null) {

      $q = JobType::select("order");
      if($max) {
        $q->where('order','<',$max);
      }
      $q->orderBy('job_types.order', 'desc');
  		$lastItem = $q->first();

  		if($lastItem) {
  			return $lastItem->order;
  		}

  		return 0;
  	}

  protected static function boot() {
        parent::boot();
        static::deleting(function($JobType) {
            // $JobType->order=0;
            // $JobType->update();
        });
    }

}
