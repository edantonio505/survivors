<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEventsLog extends Model
{	
	protected $fillable = [
		'type',
		'topic_id',
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
