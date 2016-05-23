<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'body',
        'user_id'
    ];

    public function inspired()
    {
        return $this->morphMany('App\Inspiration', 'inspirational');
    }

    public function topic()
    {
    	return $this->belongsTo('App\TopicOfTheDay');
    }


    public function user()
    {
    	return User::where('id', $this->user_id)->first();
    }
}
