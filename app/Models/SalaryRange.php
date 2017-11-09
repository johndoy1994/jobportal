<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryRange extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function salaryType(){
    	return $this->belongsTo('\App\Models\SalaryType','salary_type_id','id');
    }

    public function range() {
    	return $this->range_from." - ".$this->range_to;
    }

    public function rangeId() {
    	return $this->id;
    }
}
