<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Client;

class Checklist extends Model
{
    public function car(){
        return $this->belongsTo('App\Car');
    }

    public function driver(){
        return $this->belongsTo('App\Driver');
    }

    public function decharge(){
        return $this->belongsTo("App\Decharge");
    }

    public function restitition(){
        return $this->hasOne('App\Restitition');
    }
   
}
//$car->checklists()->where("decharge_id", "<>", null)->with("decharge")->get()