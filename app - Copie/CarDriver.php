<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Car;
use App\Driver;

class CarDriver extends Pivot
{
    function car(){
        return $this->belongsTo('App\Car');
    }
    
}
