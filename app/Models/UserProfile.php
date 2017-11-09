<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{

	const PUBLIC_PROFILE = 1;
	const PRIVATE_PROFILE = 2;
	const HIDE_PROFILE = 3;
    
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function title() {
    	return $this->hasOne('\App\Models\PersonTitle','id','person_title_id');
    }

}
