<?php

namespace App\Models;

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
        'Name', 
        'Email',
        'Password',
        'Role',
        'Qualification',
        'Specially',
        'Phone',
        'Country',
        'Address',
        'PinCode',
        'PresentWorkingPlace',
        'Achievement',
        'Publication',
        'JobChange',
        'ResumeFile',
        'Photo',
        'IsActive',
        'IsDeleted',
        'CreatedAt',
        'ModifiedAt',
    ];


    protected $hidden = [
        'password', 'remember_token',
    ];    
    
}
