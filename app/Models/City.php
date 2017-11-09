<?php

namespace App\Models;

use App\Repos\API\PublicRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;
    
    protected $dates = ["deleted_at"];
    protected $fillable = ['status'];

    public function fetchFullAddress() {

      $name = $this->getName();
      if($this->State && $this->State->status == 0) {
        $name .= ", ".$this->State->getName();
        if($this->State->Country && $this->State->Country->status == 0) {
          $name .= ", ".$this->State->Country->getName();
        }
      }
      return $name;

    }

    public function State() {
		  return $this->belongsTo('\App\Models\State','state_id', 'id');

    }

    public function getName() {
    	return ucwords($this->name);
    }

	public function getStateName()
   	{
  		return ucwords($this->state_name);
  	}
  	public function getCountryName()
   	{
  		return ucwords($this->country_name);
  	}	

    public function jobCount() {
      return PublicRepo::jobCountInCity($this->id);
    }

}
