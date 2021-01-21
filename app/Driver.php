<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        "firstname" ,
        "lastname" ,
        "tel" ,
        "code_paie" ,
        "type" ,
        "createdby_id"
    ];
}
