<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Achievements extends Authenticatable
{
    // use Notifiable;

    protected $table = 'achievements';
    public $timestamps = false;
    protected $primaryKey = 'AchievementsId';

    protected $fillable = [
        'AchievementsId', 
        'UserId', 
        'title',
        'photoAlbum',
        'videos',
        'IsActive',
        'IsDeleted',
        'CreatedAt',
        'ModifiedAt',
    ];

    


    public function userAchievements()
    {
        return $this->belongsTo('App\User','UserId');
    }
}
