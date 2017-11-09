<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWeekday extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
