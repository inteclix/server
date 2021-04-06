<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Missionvl extends Model
{
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'createdby_id');
    }

    public function remourque()
    {
        return $this->belongsTo('App\Car', 'remourque_id');
    }
    public function car()
    {
        return $this->belongsTo('App\Car', 'car_id');
    }
    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
    public function driver1()
    {
        return $this->belongsTo('App\Driver', 'driver1_id');
    }
    public function driver2()
    {
        return $this->belongsTo('App\Driver', 'driver2_id');
    }

    public function depart()
    {
        return $this->belongsTo('App\City', 'depart_id');
    }

    public function destination()
    {
        return $this->belongsTo('App\City', 'destination_id');
    }
}
