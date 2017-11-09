<?php

namespace App\Models;

use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstantMatch extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at', 'pause'];

    public function inPause() {
    	$currentDateTime = \Carbon\Carbon::now();
    	return $currentDateTime < $this->pause;
    	//return Utility::date_greaterThanToday($this->pause, "Y-m-d H:i:s");
    }

    public function user() {
    	return $this->belongsTo('\App\Models\User');
    }
}
