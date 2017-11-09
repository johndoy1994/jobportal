<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVerification extends Model
{

	// constants
	const VERIFIED = "VERIFIED";
	const NOT_VERIFIED = "NOT_VERIFIED";

	// model

    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
