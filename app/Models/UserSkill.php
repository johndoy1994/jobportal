<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSkill extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function tag() {
    	return $this->hasOne('\App\Models\Tag', 'id', 'tag_id');
    }

}
