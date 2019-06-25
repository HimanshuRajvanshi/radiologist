<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    protected $table = 'users';
    public $timestamps = false;
    protected $primaryKey = 'UserId';

    protected $fillable = [
        'UserId',
        'name', 
        'email',
        'password',
        'role',
        'qualification',
        'specially',
        'phone',
        'country',
        'address',
        'pinCode',
        'presentWorkingPlace',
        'achievement',
        'publication',
        'jobChange',
        'resumeFile',
        'photo',
        'IsActive',
        'IsDeleted',
        'CreatedAt',
        'ModifiedAt',
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];  
    
    //get all Case
    public function cases()
    {
        return $this->hasMany('App\Models\Cases', 'UserId');
    }

    //Get All Achievements
    public function achievements()
    {
        return $this->hasMany('App\Models\Achievements', 'UserId');
    }

    
}
