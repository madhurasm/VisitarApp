<?php

namespace App\Models;

use Google\Service\AlertCenter\Entity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntitySite extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function entity()
    {
        return $this->hasOne(User::class,'id','entity_id');
    }
}
