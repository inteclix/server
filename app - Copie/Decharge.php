<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Client;

class Decharge extends Model
{
    public function createdby()
    {
        return $this->belongsTo("App\User", "createdby_id");
    }

    public function client()
    {
        return $this->belongsTo("App\Client");
    }
    public function checklists()
    {
        return $this->hasMany('App\Checklist');
    }

    public function restitition()
    {
        return $this->hasOne("App\Restitition");
    }
}
