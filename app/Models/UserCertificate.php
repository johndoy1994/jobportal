<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCertificate extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getName() {
    	return ucwords($this->certificate);
    }

}
