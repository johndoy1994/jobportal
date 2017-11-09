<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;
    
    protected $dates = ["deleted_at"];

    protected $fillable = ['status'];

    public function States() {
    	return $this->hasMany('\App\Models\State')->orderBy('name');
    }

    public function getName()
 	{
		return ucwords($this->name);
  	}

  	protected static function boot() {
  		parent::boot();
  		static::deleting(function($country) {
  			$country->States()->delete();
  		});
  	}

}
