<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadedJob extends Model
{
    protected $fillable = ['user_id','job_id'];
}
