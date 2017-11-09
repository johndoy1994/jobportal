<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Experience extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getName() {
    	return ucwords($this->exp_name);
    }

   //  public static function getLastOrder() {
  	// 	$lastItem = DB::table('experiences')
  	// 			->orderBy('experiences.order', 'desc')
  	// 			->first();

  	// 	if($lastItem) {
  	// 		return $lastItem->order;
  	// 	}

  	// 	return 0;
  	// }
    public static function getFirstOrder($min=null) {

      $q = Experience::select("order");
      if($min) {
        $q->where('order','>',$min);
      }
      $q->orderBy('experiences.order');
      $lastItem = $q->first();
      
      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

    public static function getLastOrder($max=null) {

      $q = Experience::select("order");
      if($max) {
        $q->where('order','<',$max);
      }
      $q->orderBy('experiences.order', 'desc');
      $lastItem = $q->first();

      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

    protected static function boot() {
        parent::boot();
        static::deleting(function($experience) {
            // $experience->order=0;
            // $experience->update();
        });
    }

}
