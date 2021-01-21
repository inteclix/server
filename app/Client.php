<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        "code",
        "designation",
        "localite",
        "client_id",
        "createdby_id"
    ];

    public function clients()
    {
        return $this->hasMany('App\Client');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
