<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCertificate extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function getCertificateString() {
    	return $this->certificate;
    }

}
