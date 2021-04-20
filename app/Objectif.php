<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objectif extends Model
{
	public function processus()
	{
			return $this->belongsToMany('App\Processu');
	}
	public function axe()
	{
			return $this->belongsTo('App\Axe');
	}

}
