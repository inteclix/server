<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Car;

class CarState extends Pivot
{
    protected $table = 'car_states';
    public function car()
    {
        return $this->belongsTo('App\Car');
    }

    public function createdby()
    {
        return $this->belongsTo('App\User', 'createdby_id');
    }
}
