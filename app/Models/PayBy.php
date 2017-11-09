<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayBy extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function getPayById() {
        return $this->id;
    }

    public function getName() {
    	return ucwords($this->name);
    }
}
