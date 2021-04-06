<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function cars()
    {
        return $this->belongsToMany('App\Car');
    }
}
