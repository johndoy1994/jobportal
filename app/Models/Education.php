<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Education extends Model
{
    use SoftDeletes;
    
    protected $dates = ["deleted_at"];
    
    public function getName()
 	{
		return ucwords($this->name);
  	}

    public static function getFirstOrder($min=null) {

      $q = Education::select("order");
      if($min) {
        $q->where('order','>',$min);
      }
      $q->orderBy('education.order');
      $lastItem = $q->first();
      
      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

  	public static function getLastOrder($max=null) {

      $q = Education::select("order");
      if($max) {
        $q->where('order','<',$max);
      }
      $q->orderBy('education.order', 'desc');
  		$lastItem = $q->first();

  		if($lastItem) {
  			return $lastItem->order;
  		}

  		return 0;
  	}

    protected static function boot() {
        parent::boot();
        static::deleting(function($education) {
            // $education->order=0;
            // $education->update();
        });
    }

}
