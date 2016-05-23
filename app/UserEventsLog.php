<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEventsLog extends Model
{	
	protected $fillable = [
		'type',
		'userCommenter',
		'inspiredUser',
		'newConnection',
		'AcceptingUser',
		'connectionPosting'
	];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
