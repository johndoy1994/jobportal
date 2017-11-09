<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ExperienceLevel extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getName() {
    	return ucwords($this->level);
    }

   	public static function getFirstOrder($min=null) {

      $q = ExperienceLevel::select("order");
      if($min) {
        $q->where('order','>',$min);
      }
      $q->orderBy('experience_levels.order');
      $lastItem = $q->first();
      
      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

    public static function getLastOrder($max=null) {

      $q = ExperienceLevel::select("order");
      if($max) {
        $q->where('order','<',$max);
      }
      $q->orderBy('experience_levels.order', 'desc');
      $lastItem = $q->first();

      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }
}
