<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;
    
    protected $dates = ["deleted_at"];
    protected $fillable = ['status'];

    public function getCityIds($limit = 4) {
        if(isset($this->city_ids)) {
            return explode(",", $this->city_ids, $limit);
        }
        return false;
    }

    public function Country() {
		  return $this->belongsTo('\App\Models\Country','country_id', 'id');
	  }

    public function Cities() {
      return $this->hasMany('\App\Models\City')->orderBy('name');
    }

  	public function getCountryName()
   	{
  		return ucwords($this->country_name);
  	}	

    public function getName()
    {
		  return ucwords($this->name);
  	}

  protected static function boot() {
    parent::boot();
    static::deleting(function($state) {
      $state->Cities()->delete();
    });
  }
}
