<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaseTable extends Model
{
    //
	protected  $table="cases";
	protected $primaryKey = 'CaseId';
	public function comments(){  
        return $this->hasMany('App\Comment','case_id');
    }
}
