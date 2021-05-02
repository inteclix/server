<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conformite extends Model
{
	public function processu()
	{
		return $this->belongsTo('App\Processu');
	}
	public function actions()
	{
		return $this->hasMany('App\Action');
	}
	public function createdby()
	{
		return $this->belongsTo('App\User');
	}
}
