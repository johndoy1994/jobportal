<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SalaryType extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function salaryRange()
    {
    	return $this->hasMany('\App\Models\SalaryRange')->orderBy("range_from");
    }

    public function getTypeName(){
    	return ucwords($this->salary_type_name);
    }

    public function getName() {
        return ucwords($this->salary_type_name);
    }

    public function getTypeId() {
        return $this->id;
    }    

    public function perWord() {
        switch (trim(strtolower($this->salary_type_name))) {

            case 'annually': return "annum";
            case 'daily': return "day";
            case 'monthly' : return "month";
            case 'hourly' : return "hour";
            case "per project" : return "project";
            
            default:
                return trim(strtolower($this->salary_type_name));
        }
    }

    protected static function boot() {
    	parent::boot();
    	static::deleting(function($salaryType) {
            // $salaryType->order=0;
            // $salaryType->update();
    		$salaryType->salaryRange()->delete();
    	});
    }

    public static function getFirstOrder($min=null) {

      $q = SalaryType::select("order");
      if($min) {
        $q->where('order','>',$min);
      }
      $q->orderBy('salary_types.order');
      $lastItem = $q->first();
      
      if($lastItem) {
        return $lastItem->order;
      }

      return 0;
    }

    public static function getLastOrder($max=null) {

      $q = SalaryType::select("order");
      if($max) {
        $q->where('order','<',$max);
      }
      $q->orderBy('salary_types.order', 'desc');
        $lastItem = $q->first();

        if($lastItem) {
            return $lastItem->order;
        }

        return 0;
    }

}
