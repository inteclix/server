<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Client;

class Restitition extends Model
{
    public function checklist()
    {
        return $this->belongsTo('App\Checklist');
    }
}
