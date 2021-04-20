<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Processu extends Model
{
	public function indicateurs()
	{
		return $this->hasMany('App\Indicateur', "processu_id");
	}
}
