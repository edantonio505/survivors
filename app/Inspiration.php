<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inspiration extends Model
{
    //like
	public function inspirational()
	{
		return $this->morphTo();
	}


	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
