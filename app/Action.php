<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	public function conformite()
	{
		return $this->belongsTo('App\Conformite');
	}
	public function createdby()
	{
		return $this->belongsTo('App\User');
	}
}
