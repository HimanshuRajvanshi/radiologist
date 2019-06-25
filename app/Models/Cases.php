<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Cases extends Authenticatable
{
    // use Notifiable;

    protected $table = 'cases';
    public $timestamps = false;
    protected $primaryKey = 'CaseId';

    protected $fillable = [
        'CaseId', 
        'UserId', 
        'radiologistName',
        'designation',
        'caseTittle',
        'chiefComplain',
        'previewInvestigation',
        'photoAlbum',
        'videos',
        'comments',
        'rating',
        'IsActive',
        'IsDeleted',
        'CreatedAt',
        'ModifiedAt',
    ];

    public function userCase()
    {
        return $this->belongsTo('App\User','UserId');
    }

 
    
}
