<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    //
	protected $fillable=['user_id','case_id','is_liked'];
}
