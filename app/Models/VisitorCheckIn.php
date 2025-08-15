<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorCheckIn extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getProfileImageAttribute($val)
    {
        return checkFileExist($val, 'no_user_image');
    }

    public function entity()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function receptionist()
    {
        return $this->hasOne(User::class,'id','receptionist_id');
    }

    public function site()
    {
        return $this->hasOne(EntitySite::class,'id','site_id');
    }
}
