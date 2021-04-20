<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Indicateur extends Model
{
	public function objectif()
	{
			return $this->belongsTo('App\Objectif');
	}

	

	public function processu()
	{
			return $this->belongsTo('App\Processu');
	}

	public function valeurs()
	{
			return $this->hasMany('App\Indicateurv');
	}

	public function latestValeur()
{
   return $this->hasMany('App\Indicateurv')->latest();
   // order by by how ever you need it ordered to get the latest
}

}
